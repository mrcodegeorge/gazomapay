<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class AuditLogger {
    public static function log(string $action, string $description, ?array $metadata = null): void {
        try {
            $pdo = Database::getConnection();
            $merchantId = Auth::merchantId() ?: 1;
            $user = Auth::user();
            $userId = $user ? $user['id'] : null;
            $userEmail = $user ? $user['email'] : 'System';

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI/System';

            $stmt = $pdo->prepare("INSERT INTO audit_logs (merchant_id, user_id, user_email, action, ip_address, user_agent, metadata) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $merchantId,
                $userId,
                $userEmail,
                $action,
                $ip,
                $ua,
                json_encode(array_merge(['description' => $description], $metadata ?? []))
            ]);
        } catch (Exception $e) {
            error_log("Audit log failed: " . $e->getMessage());
        }
    }
}
