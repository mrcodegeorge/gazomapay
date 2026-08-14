<?php

require_once __DIR__ . '/../../config/database.php';

class WebhookDispatcher {
    public static function dispatch(int $merchantId, string $eventType, array $payload): void {
        try {
            $pdo = Database::getConnection();

            // Find endpoints subscribed to this merchant & event
            $stmt = $pdo->prepare("SELECT * FROM webhook_endpoints WHERE merchant_id = ? AND status = 'active'");
            $stmt->execute([$merchantId]);
            $endpoints = $stmt->fetchAll();

            foreach ($endpoints as $ep) {
                $subscribedEvents = json_decode($ep['events'], true) ?: [];
                if (!in_array($eventType, $subscribedEvents) && !in_array('*', $subscribedEvents)) {
                    continue;
                }

                // Simulate delivery
                $signature = hash_hmac('sha256', json_encode($payload), $ep['secret']);
                
                // Record log
                $ins = $pdo->prepare("INSERT INTO webhook_logs (merchant_id, endpoint_id, event_type, payload, response_code, response_body, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $ins->execute([
                    $merchantId,
                    $ep['id'],
                    $eventType,
                    json_encode($payload),
                    200,
                    json_encode(['received' => true, 'timestamp' => date('c')]),
                    'delivered'
                ]);
            }
        } catch (Exception $e) {
            error_log("Webhook dispatch error: " . $e->getMessage());
        }
    }

    public static function retry(int $logId): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM webhook_logs WHERE id = ?");
        $stmt->execute([$logId]);
        $log = $stmt->fetch();

        if (!$log) return false;

        $upd = $pdo->prepare("UPDATE webhook_logs SET attempt_count = attempt_count + 1, response_code = 200, status = 'delivered' WHERE id = ?");
        return $upd->execute([$logId]);
    }
}
