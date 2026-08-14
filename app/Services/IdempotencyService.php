<?php

require_once __DIR__ . '/../../config/database.php';

class IdempotencyService {

    /**
     * Check if an idempotency key exists and return cached response
     */
    public static function check(int $merchantId, string $key, string $requestPath, array $requestBody): ?array {
        $pdo = Database::getConnection();

        $hash = md5(json_encode($requestBody));
        $stmt = $pdo->prepare("SELECT * FROM idempotency_keys WHERE merchant_id = ? AND idempotency_key = ?");
        $stmt->execute([$merchantId, $key]);
        $record = $stmt->fetch();

        if (!$record) {
            return null;
        }

        // Return cached payload
        return [
            'code' => (int)$record['response_code'],
            'body' => json_decode($record['response_body'], true)
        ];
    }

    /**
     * Store request response under idempotency key (Expires in 24 hours)
     */
    public static function store(int $merchantId, string $key, string $requestPath, array $requestBody, int $responseCode, array $responseBody): void {
        $pdo = Database::getConnection();

        $hash = md5(json_encode($requestBody));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $stmt = $pdo->prepare("INSERT INTO idempotency_keys (merchant_id, idempotency_key, request_path, request_hash, response_code, response_body, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $merchantId,
            $key,
            $requestPath,
            $hash,
            $responseCode,
            json_encode($responseBody),
            $expiresAt
        ]);
    }
}
