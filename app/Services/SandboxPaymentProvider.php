<?php

require_once __DIR__ . '/PaymentProviderInterface.php';
require_once __DIR__ . '/SandboxPaymentGateway.php';

class SandboxPaymentProvider implements PaymentProviderInterface {

    private SandboxPaymentGateway $gateway;

    public function __construct() {
        $this->gateway = new SandboxPaymentGateway();
    }

    public function initializePayment(array $params): array {
        return [
            'status' => 'success',
            'provider' => 'sandbox',
            'authorization_url' => '/pay/' . ($params['reference'] ?? 'GZM_DEMO'),
            'reference' => $params['reference'] ?? ('GZM_SBX_' . time())
        ];
    }

    public function charge(array $params): array {
        return $this->gateway->charge($params);
    }

    public function verifyPayment(string $reference): array {
        return $this->getTransactionStatus($reference);
    }

    public function refund(string $reference, float $amount): array {
        return $this->gateway->refund($reference, $amount);
    }

    public function getTransactionStatus(string $reference): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT reference, amount, net_amount, fee_amount, status, payment_method, created_at FROM transactions WHERE reference = ?");
        $stmt->execute([$reference]);
        $tx = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tx) {
            return [
                'success' => false,
                'status' => 'not_found',
                'message' => 'Transaction not found'
            ];
        }

        return [
            'success' => true,
            'status' => $tx['status'],
            'reference' => $tx['reference'],
            'amount' => (float)$tx['amount'],
            'net_amount' => (float)$tx['net_amount'],
            'fee_amount' => (float)$tx['fee_amount'],
            'raw_data' => $tx
        ];
    }

    public function handleWebhook(array $headers, string $rawPayload): array {
        $data = json_decode($rawPayload, true) ?? [];
        return [
            'valid' => true,
            'event_type' => $data['event'] ?? 'payment.success',
            'reference' => $data['reference'] ?? ($data['data']['reference'] ?? ''),
            'raw' => $data
        ];
    }

    public function getProviderName(): string {
        return 'sandbox';
    }
}
