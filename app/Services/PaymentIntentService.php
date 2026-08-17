<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/PaymentProviderResolver.php';
require_once __DIR__ . '/LedgerEngine.php';
require_once __DIR__ . '/RiskEngine.php';
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/../../config/env.php';

class PaymentIntentService {

    /**
     * Create a new persistent Payment object (Stripe-style Payment Intent).
     */
    public static function create(int $merchantId, array $data): array {
        $pdo = Database::getConnection();

        $publicId = 'pay_' . bin2hex(random_bytes(12));
        $amountFloat = (float)($data['amount'] ?? 0);
        $amountPesewas = (int)round($amountFloat * 100); // Integer Minor Units
        $currency = strtoupper($data['currency'] ?? 'GHS');
        $description = trim($data['description'] ?? 'Gazoma Pay Payment');
        $customerId = !empty($data['customer_id']) ? (int)$data['customer_id'] : null;
        $livemode = Env::get('GAZOMA_PAYMENT_MODE') === 'live' ? 1 : 0;

        $stmt = $pdo->prepare("
            INSERT INTO payments (public_id, merchant_id, customer_id, amount, currency, status, description, livemode, metadata) 
            VALUES (?, ?, ?, ?, ?, 'requires_payment_method', ?, ?, ?)
        ");
        $stmt->execute([
            $publicId,
            $merchantId,
            $customerId,
            $amountPesewas,
            $currency,
            $description,
            $livemode,
            json_encode($data['metadata'] ?? [])
        ]);

        $paymentId = (int)$pdo->lastInsertId();

        return self::get($publicId);
    }

    /**
     * Get Payment Intent details by public_id.
     */
    public static function get(string $publicId): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM payments WHERE public_id = ?");
        $stmt->execute([$publicId]);
        $pay = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pay) {
            return null;
        }

        // Fetch Attempts
        $stmtAtt = $pdo->prepare("SELECT * FROM payment_attempts WHERE payment_id = ? ORDER BY id DESC");
        $stmtAtt->execute([$pay['id']]);
        $attempts = $stmtAtt->fetchAll(PDO::FETCH_ASSOC);

        $pay['amount_float'] = round($pay['amount'] / 100, 2);
        $pay['attempts'] = $attempts;
        $pay['metadata'] = json_decode($pay['metadata'] ?? '[]', true);

        return $pay;
    }

    /**
     * Confirm & Execute a Payment Intent.
     */
    public static function confirm(string $publicId, array $paymentMethodParams): array {
        $pdo = Database::getConnection();
        $pay = self::get($publicId);

        if (!$pay) {
            return ['success' => false, 'error_code' => 'RESOURCE_NOT_FOUND', 'message' => 'Payment intent not found'];
        }

        if (in_array($pay['status'], ['succeeded', 'canceled'])) {
            return ['success' => false, 'error_code' => 'INVALID_STATE', 'message' => "Payment is already {$pay['status']}"];
        }

        $merchantId = (int)$pay['merchant_id'];
        $amountFloat = $pay['amount_float'];
        $paymentMethod = $paymentMethodParams['payment_method'] ?? 'card';
        $customerEmail = $paymentMethodParams['customer_email'] ?? 'customer@example.com';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // 1. Evaluate Risk Engine
        $risk = RiskEngine::evaluate($merchantId, $amountFloat, $customerEmail, $ip);
        if (!$risk['allowed']) {
            self::updateStatus($pay['id'], 'failed');
            return [
                'success' => false,
                'error_code' => 'RISK_BLOCKED',
                'message' => 'Payment blocked by automated risk engine: ' . implode('; ', $risk['reasons'])
            ];
        }

        // 2. Create Payment Attempt
        $attemptPublicId = 'att_' . bin2hex(random_bytes(10));
        $providerName = Env::get('GAZOMA_PAYMENT_MODE') === 'live' ? 'paystack' : 'sandbox';
        
        $stmtAtt = $pdo->prepare("
            INSERT INTO payment_attempts (public_id, payment_id, provider, payment_method, status) 
            VALUES (?, ?, ?, ?, 'processing')
        ");
        $stmtAtt->execute([$attemptPublicId, $pay['id'], $providerName, $paymentMethod]);
        $attemptId = (int)$pdo->lastInsertId();

        self::updateStatus($pay['id'], 'processing');

        // 3. Dispatch to Provider Resolver
        $provider = PaymentProviderResolver::resolve($providerName);
        $chargeRes = $provider->charge(array_merge($paymentMethodParams, [
            'merchant_id' => $merchantId,
            'amount' => $amountFloat,
            'reference' => $publicId
        ]));

        if (!empty($chargeRes['success']) || ($chargeRes['status'] ?? '') === 'successful' || ($chargeRes['status'] ?? '') === 'succeeded') {
            // Update Attempt
            $pdo->prepare("UPDATE payment_attempts SET status = 'succeeded', provider_reference = ? WHERE id = ?")
                ->execute([$chargeRes['reference'] ?? $publicId, $attemptId]);

            // Update Payment Intent & Ledger
            self::updateStatus($pay['id'], 'succeeded', $providerName, $chargeRes['reference'] ?? $publicId, $paymentMethod);

            // Record Double-Entry Ledger Entry
            $feeCalc = FeeEngine::calculate($amountFloat);
            LedgerEngine::recordPayment($merchantId, $publicId, $amountFloat, $feeCalc['fee'], $feeCalc['net_amount'], "Payment charge for {$publicId}");
            
            // Update Merchant Stored Available Balance
            $pdo->prepare("UPDATE merchants SET available_balance = available_balance + ? WHERE id = ?")
                ->execute([$feeCalc['net_amount'], $merchantId]);

            return [
                'success' => true,
                'status' => 'succeeded',
                'payment' => self::get($publicId)
            ];
        } else {
            $failReason = $chargeRes['message'] ?? 'Payment execution failed';
            $pdo->prepare("UPDATE payment_attempts SET status = 'failed', failure_message = ? WHERE id = ?")
                ->execute([$failReason, $attemptId]);

            self::updateStatus($pay['id'], 'failed');

            return [
                'success' => false,
                'status' => 'failed',
                'error_code' => 'PAYMENT_FAILED',
                'message' => $failReason
            ];
        }
    }

    /**
     * Cancel a Payment Intent.
     */
    public static function cancel(string $publicId): array {
        $pdo = Database::getConnection();
        $pay = self::get($publicId);

        if (!$pay) {
            return ['success' => false, 'error_code' => 'RESOURCE_NOT_FOUND', 'message' => 'Payment intent not found'];
        }

        if (in_array($pay['status'], ['succeeded', 'canceled'])) {
            return ['success' => false, 'error_code' => 'INVALID_STATE', 'message' => "Payment is already {$pay['status']}"];
        }

        self::updateStatus($pay['id'], 'canceled');

        return [
            'success' => true,
            'status' => 'canceled',
            'payment' => self::get($publicId)
        ];
    }

    private static function updateStatus(int $paymentId, string $status, string $provider = 'sandbox', string $providerRef = '', string $method = ''): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE payments 
            SET status = ?, provider = ?, provider_reference = ?, payment_method = COALESCE(?, payment_method), updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$status, $provider, $providerRef, $method ?: null, $paymentId]);
    }
}
