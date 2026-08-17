<?php

require_once __DIR__ . '/PaymentProviderInterface.php';
require_once __DIR__ . '/../../config/env.php';

class HubtelPaymentProvider implements PaymentProviderInterface {

    private string $clientId;
    private string $clientSecret;
    private bool $enabled;

    public function __construct() {
        $this->clientId = Env::get('HUBTEL_CLIENT_ID', '');
        $this->clientSecret = Env::get('HUBTEL_CLIENT_SECRET', '');
        $this->enabled = (bool)Env::get('HUBTEL_ENABLED', false);
    }

    public function initializePayment(array $params): array {
        return $this->charge($params);
    }

    public function charge(array $params): array {
        if (!$this->enabled || empty($this->clientId) || empty($this->clientSecret)) {
            return [
                'success' => false,
                'status' => 'not_configured',
                'message' => 'Hubtel payment provider is not configured or disabled.'
            ];
        }

        $payload = [
            'totalAmount' => (float)($params['amount'] ?? 0),
            'description' => $params['description'] ?? 'Gazoma Pay Mobile Money Payment',
            'callbackUrl' => $params['callback_url'] ?? '',
            'returnUrl' => $params['return_url'] ?? '',
            'merchantAccountNumber' => $params['merchant_account_number'] ?? '2019284',
            'clientReference' => $params['reference'] ?? ('GZM_HUB_' . time())
        ];

        return $this->request('POST', '/merchantaccount/onlinecheckout/invoice/create', $payload);
    }

    public function verifyPayment(string $reference): array {
        if (!$this->enabled || empty($this->clientId)) {
            return [
                'success' => false,
                'status' => 'not_configured',
                'message' => 'Hubtel provider disabled.'
            ];
        }

        return $this->request('GET', '/merchantaccount/onlinecheckout/invoice/status/' . rawurlencode($reference));
    }

    public function refund(string $reference, float $amount): array {
        return [
            'success' => false,
            'status' => 'not_supported',
            'message' => 'Hubtel automated refunds require manual merchant portal submission.'
        ];
    }

    public function getTransactionStatus(string $reference): array {
        return $this->verifyPayment($reference);
    }

    public function handleWebhook(array $headers, string $rawPayload): array {
        $data = json_decode($rawPayload, true) ?? [];
        return [
            'valid' => true,
            'event_type' => $data['ResponseCode'] === '0000' ? 'payment.success' : 'payment.failed',
            'reference' => $data['Data']['ClientReference'] ?? '',
            'raw' => $data
        ];
    }

    public function getProviderName(): string {
        return 'hubtel';
    }

    private function request(string $method, string $path, array $data = []): array {
        $auth = base64_encode($this->clientId . ':' . $this->clientSecret);
        $ch = curl_init('https://api-momo.hubtel.com' . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . $auth,
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 15
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $res = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'message' => 'Hubtel API Connection Error: ' . $err];
        }

        $decoded = json_decode($res, true) ?? [];
        return [
            'success' => ($decoded['ResponseCode'] ?? '') === '0000',
            'data' => $decoded['Data'] ?? [],
            'message' => $decoded['Message'] ?? '',
            'raw' => $decoded
        ];
    }
}
