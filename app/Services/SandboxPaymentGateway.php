<?php

require_once __DIR__ . '/PaymentGatewayInterface.php';
require_once __DIR__ . '/FeeEngine.php';
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/WebhookDispatcher.php';
require_once __DIR__ . '/../../config/database.php';

class SandboxPaymentGateway implements PaymentGatewayInterface {
    
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

        // 1. Find or create customer
        $stmtCust = $pdo->prepare("SELECT id FROM customers WHERE merchant_id = ? AND email = ?");
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

        // 3. Generate Reference
        $reference = 'GZM_' . sprintf('%08d', rand(100000, 99999999));
        $status = $forceFailure ? 'failed' : 'successful';
        $failureReason = $forceFailure ? 'Card declined by simulated issuing bank' : null;

        // 4. Create Transaction Record
        $stmtTx = $pdo->prepare("INSERT INTO transactions (reference, merchant_id, customer_id, payment_link_id, invoice_id, amount, fee, net_amount, currency, payment_method, provider, status, failure_reason, ip_address, metadata) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $metadata = json_encode([
            'sandbox' => true,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'channel' => $paymentMethod
        ]);

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $stmtTx->execute([
            $reference,
            $merchantId,
            $customerId,
            $paymentLinkId,
            $invoiceId,
            $amount,
            $fee,
            $net,
            'GHS',
            $paymentMethod,
            'Sandbox Gateway',
            $status,
            $failureReason,
            $ip,
            $metadata
        ]);

        $transactionId = $pdo->lastInsertId();

        if ($status === 'successful') {
            // Update Merchant Available Balance & Total Revenue
            $updMch = $pdo->prepare("UPDATE merchants SET available_balance = available_balance + ? WHERE id = ?");
            $updMch->execute([$net, $merchantId]);

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

            // Audit log
            AuditLogger::log('payment.success', "Payment {$reference} for GH₵ {$amount} succeeded via {$paymentMethod}", ['transaction_id' => $transactionId]);

            // Dispatch Webhook
            WebhookDispatcher::dispatch($merchantId, 'payment.success', [
                'event' => 'payment.success',
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
        } else {
            // Update Customer failed payment count
            $updCst = $pdo->prepare("UPDATE customers SET total_transactions = total_transactions + 1, failed_payments = failed_payments + 1 WHERE id = ?");
            $updCst->execute([$customerId]);

            AuditLogger::log('payment.failed', "Payment {$reference} failed: {$failureReason}");

            WebhookDispatcher::dispatch($merchantId, 'payment.failed', [
                'event' => 'payment.failed',
                'reference' => $reference,
                'reason' => $failureReason
            ]);
        }

        return [
            'success' => ($status === 'successful'),
            'transaction_id' => $transactionId,
            'reference' => $reference,
            'amount' => $amount,
            'fee' => $fee,
            'net_amount' => $net,
            'currency' => 'GHS',
            'status' => $status,
            'message' => ($status === 'successful') ? 'Payment processed successfully' : $failureReason
        ];
    }

    public function refund(string $transactionReference, float $amount, string $reason = ''): array {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE reference = ? OR id = ?");
        $stmt->execute([$transactionReference, $transactionReference]);
        $tx = $stmt->fetch();

        if (!$tx) {
            return ['success' => false, 'message' => 'Transaction not found'];
        }

        if ($tx['status'] !== 'successful') {
            return ['success' => false, 'message' => 'Only successful transactions can be refunded'];
        }

        $merchantId = $tx['merchant_id'];
        $refundAmount = ($amount > 0) ? min($amount, (float)$tx['amount']) : (float)$tx['amount'];
        $refundRef = 'RFD_' . sprintf('%08d', rand(100000, 99999999));

        // Create Refund Record
        $stmtRfd = $pdo->prepare("INSERT INTO refunds (transaction_id, merchant_id, refund_reference, amount, reason, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtRfd->execute([$tx['id'], $merchantId, $refundRef, $refundAmount, $reason ?: 'Merchant initiated refund', 'completed']);

        // Update Transaction Status
        $updTx = $pdo->prepare("UPDATE transactions SET status = 'refunded' WHERE id = ?");
        $updTx->execute([$tx['id']]);

        // Deduct from Merchant Available Balance
        $updMch = $pdo->prepare("UPDATE merchants SET available_balance = available_balance - ? WHERE id = ?");
        $updMch->execute([$refundAmount, $merchantId]);

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
