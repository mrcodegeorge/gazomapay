<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Helpers/Response.php';

class ApiAuthMiddleware {
    public static function authenticate(): array {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? null;

        if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
            Response::json(['error' => 'Unauthorized: Missing or invalid Bearer token'], 401);
        }

        $token = trim(substr($authHeader, 7));

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT a.*, m.id as merchant_id, m.name as merchant_name, m.merchant_id as merchant_code FROM api_keys a JOIN merchants m ON a.merchant_id = m.id WHERE (a.public_key = ? OR a.secret_key_preview = ?) AND a.status = 'active'");
        $stmt->execute([$token, $token]);
        $apiKey = $stmt->fetch();

        if (!$apiKey) {
            Response::json(['error' => 'Unauthorized: Invalid API key'], 401);
        }

        return $apiKey;
    }
}
