<?php

require_once __DIR__ . '/PaymentGatewayInterface.php';
require_once __DIR__ . '/FeeEngine.php';
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/WebhookDispatcher.php';
require_once __DIR__ . '/LedgerEngine.php';
require_once __DIR__ . '/../../config/database.php';

class SandboxPaymentGateway implements PaymentGatewayInterface {
    
    /**
     * Auto-detect Card Brand from BIN / Card Number
     */
    public static function detectCardBrand(string $cardNumber): string {
        $clean = preg_replace('/[^0-9]/', '', $cardNumber);
        if (empty($clean)) {
            return 'Visa';
        }
        if (strpos($clean, '4') === 0) {
            return 'Visa';
        }
        $prefix2 = (int)substr($clean, 0, 2);
        if (($prefix2 >= 51 && $prefix2 <= 55) || ($prefix2 >= 22 && $prefix2 <= 27)) {
            return 'Mastercard';
        }
        if (in_array($prefix2, [34, 37])) {
            return 'American Express';
        }
        if (strpos($clean, '6011') === 0 || strpos($clean, '65') === 0) {
            return 'Discover';
        }
        return 'Visa';
    }

    public function charge(array $paymentData): array {
        $pdo = Database::getConnection();

        $merchantId = (int)($paymentData['merchant_id'] ?? 1);
        $amount = (float)($paymentData['amount'] ?? 0);
        $paymentMethod = $paymentData['payment_method'] ?? 'card';
        $customerName = trim($paymentData['customer_name'] ?? 'Guest Payer');
        $customerEmail = trim($paymentData['customer_email'] ?? 'guest@example.com');
        $customerPhone = trim($paymentData['customer_phone'] ?? '');
        $paymentLinkId = !empty($paymentData['payment_link_id']) ? (int)$paymentData['payment_link_id'] : null;
        $invoiceId = !empty($paymentData['invoice_id']) ? (int)$paymentData['invoice_id'] : null;
        $forceFailure = !empty($paymentData['force_failure']);
        $require3ds = !empty($paymentData['require_3ds']);
        $suppliedOtp = trim($paymentData['otp'] ?? '');

        // Check merchant account status
        $stmtMchStatus = $pdo->prepare("SELECT account_status FROM merchants WHERE id = ?");
        $stmtMchStatus->execute([$merchantId]);
        $mchStatus = $stmtMchStatus->fetchColumn();

        if ($mchStatus === 'suspended' || $mchStatus === 'closed') {
            return [
                'success' => false,
                'message' => 'Merchant account is currently suspended or restricted from accepting transactions.'
            ];
        }

        // Card Details & Masking
        $cardNumber = $paymentData['card_number'] ?? '4000 1234 5678 9010';
        $cleanCard = preg_replace('/[^0-9]/', '', $cardNumber);
        $last4 = strlen($cleanCard) >= 4 ? substr($cleanCard, -4) : '9010';
        $maskedCard = '**** **** **** ' . $last4;
        $cardBrand = self::detectCardBrand($cleanCard);
        $cardExpiry = $paymentData['card_expiry'] ?? '12/28';
        $cardToken = 'card_tok_' . bin2hex(random_bytes(12));

        // Begin atomic database transaction with row locking
        $pdo->beginTransaction();

        try {
            // 1. Find or create customer with lock
            $stmtCust = $pdo->prepare("SELECT id FROM customers WHERE merchant_id = ? AND email = ? FOR UPDATE");
            $stmtCust->execute([$merchantId, $customerEmail]);
            $customer = $stmtCust->fetch();

            if ($customer) {
                $customerId = $customer['id'];
            } else {
                $uuid = 'cst_' . bin2hex(random_bytes(6));
                $insCust = $pdo->prepare("INSERT INTO customers (merchant_id, uuid, name, email, phone) VALUES (?, ?, ?, ?, ?)");
                $insCust->execute([$merchantId, $uuid, $customerName, $customerEmail, $customerPhone]);
                $customerId = $pdo->lastInsertId();
            }

            // 2. Fee Calculation
            $feeResult = FeeEngine::calculate($amount);
            $fee = $feeResult['fee'];
            $net = $feeResult['net_amount'];

            // 3. Generate Reference & Event ID
            $reference = 'GZM_' . sprintf('%08d', rand(100000, 99999999));
            $eventId = 'evt_' . bin2hex(random_bytes(10));

            // Determine status (If 3DS is required and OTP not yet verified)
            $is3DsChallenge = ($paymentMethod === 'card' && $require3ds && $suppliedOtp !== '123456');

            if ($forceFailure) {
                $status = 'failed';
                $failureReason = 'Card declined by simulated issuing bank';
            } elseif ($is3DsChallenge) {
                $status = 'pending_3ds';
                $failureReason = null;
            } else {
                $status = 'successful';
                $failureReason = null;
            }

            // 4. Create Transaction Record
            $stmtTx = $pdo->prepare("INSERT INTO transactions (reference, event_id, merchant_id, customer_id, payment_link_id, invoice_id, amount, fee, net_amount, currency, payment_method, provider, status, failure_reason, ip_address, metadata) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $metadataArray = [
                'sandbox' => true,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'channel' => $paymentMethod
            ];

            if ($paymentMethod === 'card') {
                $metadataArray['card'] = [
                    'brand' => $cardBrand,
                    'masked_card' => $maskedCard,
                    'last4' => $last4,
                    'expiry' => $cardExpiry,
                    'card_token' => $cardToken,
                    'three_d_secure' => $is3DsChallenge ? 'required' : 'passed'
                ];
            } else {
                $metadataArray['mobile_money'] = [
                    'provider' => $paymentData['provider'] ?? 'mtn',
                    'phone' => $customerPhone
                ];
            }

            $metadata = json_encode($metadataArray);
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $providerName = ($paymentMethod === 'card') ? "Issuing Bank ({$cardBrand})" : 'Sandbox Gateway';

            $stmtTx->execute([
                $reference,
                $eventId,
                $merchantId,
                $customerId,
                $paymentLinkId,
                $invoiceId,
                $amount,
                $fee,
                $net,
                'GHS',
                $paymentMethod,
                $providerName,
                $status,
                $failureReason,
                $ip,
                $metadata
            ]);

            $transactionId = $pdo->lastInsertId();

            if ($status === 'successful') {
                // Record in Double-Entry Financial Ledger
                LedgerEngine::recordPayment($merchantId, $reference, $amount, $fee, $net, "Payment {$reference} from {$customerName}");

                // Update Merchant Available Balance directly from Ledger Engine
                $newAvailable = LedgerEngine::getAvailableBalance($merchantId);
                $updMch = $pdo->prepare("UPDATE merchants SET available_balance = ? WHERE id = ?");
                $updMch->execute([$newAvailable, $merchantId]);

                // Update Customer Stats
                $updCst = $pdo->prepare("UPDATE customers SET total_transactions = total_transactions + 1, total_spending = total_spending + ?, successful_payments = successful_payments + 1 WHERE id = ?");
                $updCst->execute([$amount, $customerId]);

                // Update Payment Link Usage
                if ($paymentLinkId) {
                    $updPL = $pdo->prepare("UPDATE payment_links SET usage_count = usage_count + 1 WHERE id = ?");
                    $updPL->execute([$paymentLinkId]);
                }

                // Update Invoice Status if attached
                if ($invoiceId) {
                    $updInv = $pdo->prepare("UPDATE invoices SET status = 'paid' WHERE id = ?");
                    $updInv->execute([$invoiceId]);
                }
            } elseif ($status === 'failed') {
                $updCst = $pdo->prepare("UPDATE customers SET total_transactions = total_transactions + 1, failed_payments = failed_payments + 1 WHERE id = ?");
                $updCst->execute([$customerId]);
            }

            $pdo->commit();

            if ($status === 'successful') {
                AuditLogger::log('payment.success', "Payment {$reference} for GH₵ {$amount} succeeded via {$paymentMethod}", ['transaction_id' => $transactionId]);

                WebhookDispatcher::dispatch($merchantId, 'payment.success', [
                    'event' => 'payment.success',
                    'event_id' => $eventId,
                    'transaction_id' => $transactionId,
                    'reference' => $reference,
                    'amount' => $amount,
                    'fee' => $fee,
                    'net_amount' => $net,
                    'customer' => [
                        'name' => $customerName,
                        'email' => $customerEmail
                    ]
                ]);
            } elseif ($status === 'pending_3ds') {
                AuditLogger::log('payment.3ds_initiated', "3DS verification requested for Card transaction {$reference}");
            } else {
                AuditLogger::log('payment.failed', "Payment {$reference} failed: {$failureReason}");

                WebhookDispatcher::dispatch($merchantId, 'payment.failed', [
                    'event' => 'payment.failed',
                    'event_id' => $eventId,
                    'reference' => $reference,
                    'reason' => $failureReason
                ]);
            }

            return [
                'success' => ($status === 'successful' || $status === 'pending_3ds'),
                'transaction_id' => $transactionId,
                'reference' => $reference,
                'event_id' => $eventId,
                'amount' => $amount,
                'fee' => $fee,
                'net_amount' => $net,
                'currency' => 'GHS',
                'status' => $status,
                'requires_3ds' => ($status === 'pending_3ds'),
                'card_token' => $cardToken,
                'card_brand' => $cardBrand,
                'masked_card' => $maskedCard,
                'message' => ($status === 'pending_3ds') ? '3D Secure OTP verification required' : (($status === 'successful') ? 'Payment processed successfully' : $failureReason)
            ];

        } catch (Exception $e) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Payment processing failed due to a system error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify 3D Secure OTP for Card Payment
     */
    public function verify3DsOtp(string $reference, string $otp): array {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE reference = ? OR id = ?");
        $stmt->execute([$reference, $reference]);
        $tx = $stmt->fetch();

        if (!$tx) {
            return ['success' => false, 'message' => 'Transaction reference not found'];
        }

        if ($tx['status'] === 'successful') {
            return ['success' => true, 'status' => 'successful', 'reference' => $tx['reference'], 'message' => 'Transaction already completed'];
        }

        if ($otp !== '123456') {
            return ['success' => false, 'message' => 'Invalid 3DS One-Time Password. Please use sandbox OTP: 123456'];
        }

        $pdo->beginTransaction();
        try {
            $merchantId = $tx['merchant_id'];
            $amount = (float)$tx['amount'];
            $fee = (float)$tx['fee'];
            $net = (float)$tx['net_amount'];
            $customerId = $tx['customer_id'];

            // Update Transaction Status
            $upd = $pdo->prepare("UPDATE transactions SET status = 'successful', updated_at = NOW() WHERE id = ?");
            $upd->execute([$tx['id']]);

            // Update Metadata
            $meta = json_decode($tx['metadata'] ?? '{}', true);
            if (isset($meta['card'])) {
                $meta['card']['three_d_secure'] = 'passed';
            }
            $updMeta = $pdo->prepare("UPDATE transactions SET metadata = ? WHERE id = ?");
            $updMeta->execute([json_encode($meta), $tx['id']]);

            // Record in Double-Entry Financial Ledger
            LedgerEngine::recordPayment($merchantId, $tx['reference'], $amount, $fee, $net, "Card 3DS Payment {$tx['reference']}");

            // Update Merchant Available Balance
            $newAvailable = LedgerEngine::getAvailableBalance($merchantId);
            $updMch = $pdo->prepare("UPDATE merchants SET available_balance = ? WHERE id = ?");
            $updMch->execute([$newAvailable, $merchantId]);

            // Update Customer Stats
            if ($customerId) {
                $updCst = $pdo->prepare("UPDATE customers SET total_transactions = total_transactions + 1, total_spending = total_spending + ?, successful_payments = successful_payments + 1 WHERE id = ?");
                $updCst->execute([$amount, $customerId]);
            }

            $pdo->commit();

            AuditLogger::log('payment.3ds_success', "3DS OTP verified successfully for Card transaction {$tx['reference']}");

            WebhookDispatcher::dispatch($merchantId, 'payment.success', [
                'event' => 'payment.success',
                'event_id' => $tx['event_id'],
                'transaction_id' => $tx['id'],
                'reference' => $tx['reference'],
                'amount' => $amount,
                'fee' => $fee,
                'net_amount' => $net
            ]);

            return [
                'success' => true,
                'status' => 'successful',
                'reference' => $tx['reference'],
                'amount' => $amount,
                'message' => '3D Secure OTP verified and payment authorized successfully'
            ];

        } catch (Exception $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => '3DS verification failed: ' . $e->getMessage()];
        }
    }

    public function refund(string $transactionReference, float $amount, string $reason = ''): array {
        $pdo = Database::getConnection();

        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("SELECT * FROM transactions WHERE reference = ? OR id = ? FOR UPDATE");
            $stmt->execute([$transactionReference, $transactionReference]);
            $tx = $stmt->fetch();

            if (!$tx) {
                $pdo->rollBack();
                return ['success' => false, 'message' => 'Transaction not found'];
            }

            if ($tx['status'] !== 'successful') {
                $pdo->rollBack();
                return ['success' => false, 'message' => 'Only successful transactions can be refunded'];
            }

            $merchantId = $tx['merchant_id'];
            $refundAmount = ($amount > 0) ? min($amount, (float)$tx['amount']) : (float)$tx['amount'];
            $refundFee = round($refundAmount * 0.015 + 0.50, 2);
            $refundNet = $refundAmount - $refundFee;
            $refundRef = 'RFD_' . sprintf('%08d', rand(100000, 99999999));

            // Create Refund Record
            $stmtRfd = $pdo->prepare("INSERT INTO refunds (transaction_id, merchant_id, refund_reference, amount, reason, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtRfd->execute([$tx['id'], $merchantId, $refundRef, $refundAmount, $reason ?: 'Merchant initiated refund', 'completed']);

            // Update Transaction Status
            $updTx = $pdo->prepare("UPDATE transactions SET status = 'refunded' WHERE id = ?");
            $updTx->execute([$tx['id']]);

            // Record Reversal in Double-Entry Financial Ledger
            LedgerEngine::recordRefund($merchantId, $refundRef, $refundAmount, $refundFee, $refundNet, "Refund reversal for {$tx['reference']}");

            // Recalculate Merchant Available Balance from Ledger
            $newAvailable = LedgerEngine::getAvailableBalance($merchantId);
            $updMch = $pdo->prepare("UPDATE merchants SET available_balance = ? WHERE id = ?");
            $updMch->execute([$newAvailable, $merchantId]);

            $pdo->commit();

            AuditLogger::log('payment.refunded', "Refund {$refundRef} issued for transaction {$tx['reference']}");

            WebhookDispatcher::dispatch($merchantId, 'payment.refunded', [
                'event' => 'payment.refunded',
                'transaction_reference' => $tx['reference'],
                'refund_reference' => $refundRef,
                'amount' => $refundAmount
            ]);

            return [
                'success' => true,
                'refund_reference' => $refundRef,
                'amount' => $refundAmount,
                'message' => 'Refund processed successfully'
            ];

        } catch (Exception $e) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Refund processing failed: ' . $e->getMessage()
            ];
        }
    }

    public function verify(string $transactionReference): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE reference = ?");
        $stmt->execute([$transactionReference]);
        $tx = $stmt->fetch();

        if (!$tx) {
            return ['success' => false, 'message' => 'Transaction not found'];
        }

        return [
            'success' => true,
            'data' => $tx
        ];
    }
}
