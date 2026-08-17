<?php

require_once __DIR__ . '/PaymentProviderInterface.php';
require_once __DIR__ . '/../../config/env.php';

class PaystackPaymentProvider implements PaymentProviderInterface {

    private string $secretKey;
    private string $publicKey;
    private bool $enabled;

    public function __construct() {
        $this->secretKey = Env::get('PAYSTACK_SECRET_KEY', '');
        $this->publicKey = Env::get('PAYSTACK_PUBLIC_KEY', '');
        $this->enabled = (bool)Env::get('PAYSTACK_ENABLED', false);
    }

    public function initializePayment(array $params): array {
        if (!$this->enabled || empty($this->secretKey)) {
            return [
                'success' => false,
                'status' => 'not_configured',
                'message' => 'Paystack provider is not configured or disabled.'
            ];
        }

        $payload = [
            'amount' => (int)round(($params['amount'] ?? 0) * 100), // In kobo/pesewas
            'email' => $params['customer_email'] ?? 'customer@example.com',
            'reference' => $params['reference'] ?? ('GZM_PST_' . time()),
            'currency' => $params['currency'] ?? 'GHS',
            'callback_url' => $params['callback_url'] ?? ''
        ];

        return $this->request('POST', '/transaction/initialize', $payload);
    }

    public function charge(array $params): array {
        if (!$this->enabled || empty($this->secretKey)) {
            return [
                'success' => false,
                'status' => 'not_configured',
                'message' => 'Paystack provider is disabled or missing credentials.'
            ];
        }

        $channel = $params['payment_method'] ?? 'card';
        if ($channel === 'mobile_money') {
            $payload = [
                'amount' => (int)round(($params['amount'] ?? 0) * 100),
                'email' => $params['customer_email'] ?? 'customer@example.com',
                'currency' => $params['currency'] ?? 'GHS',
                'mobile_money' => [
                    'phone' => $params['customer_phone'] ?? '',
                    'provider' => strtolower($params['provider'] ?? 'mtn')
                ]
            ];
            return $this->request('POST', '/charge', $payload);
        }

        return $this->initializePayment($params);
    }

    public function verifyPayment(string $reference): array {
        if (!$this->enabled || empty($this->secretKey)) {
            return [
                'success' => false,
                'status' => 'not_configured',
                'message' => 'Paystack provider is disabled.'
            ];
        }

        return $this->request('GET', '/transaction/verify/' . rawurlencode($reference));
    }

    public function refund(string $reference, float $amount): array {
        if (!$this->enabled || empty($this->secretKey)) {
            return [
                'success' => false,
                'status' => 'not_configured',
                'message' => 'Paystack provider is disabled.'
            ];
        }

        $payload = [
            'transaction' => $reference,
            'amount' => (int)round($amount * 100)
        ];

        return $this->request('POST', '/refund', $payload);
    }

    public function getTransactionStatus(string $reference): array {
        return $this->verifyPayment($reference);
    }

    public function handleWebhook(array $headers, string $rawPayload): array {
        $signature = $headers['x-paystack-signature'] ?? ($headers['X-Paystack-Signature'] ?? '');
        $computedSig = hash_hmac('sha256', $rawPayload, $this->secretKey);

        $valid = hash_equals($computedSig, $signature);
        $data = json_decode($rawPayload, true) ?? [];

        return [
            'valid' => $valid,
            'event_type' => $data['event'] ?? 'unknown',
            'reference' => $data['data']['reference'] ?? '',
            'raw' => $data
        ];
    }

    public function getProviderName(): string {
        return 'paystack';
    }

    private function request(string $method, string $path, array $data = []): array {
        $ch = curl_init('https://api.paystack.co' . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->secretKey,
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
            return ['success' => false, 'message' => 'Paystack API Connection Error: ' . $err];
        }

        $decoded = json_decode($res, true) ?? [];
        return [
            'success' => !empty($decoded['status']),
            'data' => $decoded['data'] ?? [],
            'message' => $decoded['message'] ?? '',
            'raw' => $decoded
        ];
    }
}
