<?php

require_once __DIR__ . '/../../config/database.php';

class WebhookDispatcher {

    /**
     * Dispatch event payload to active merchant webhook endpoints
     */
    public static function dispatch(int $merchantId, string $eventType, array $data): void {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM webhook_endpoints WHERE merchant_id = ? AND status = 'active'");
        $stmt->execute([$merchantId]);
        $endpoints = $stmt->fetchAll();

        if (empty($endpoints)) {
            return;
        }

        $eventId = $data['event_id'] ?? ('evt_' . bin2hex(random_bytes(12)));
        $timestamp = time();

        $payload = [
            'id' => $eventId,
            'event' => $eventType,
            'created_at' => date('c', $timestamp),
            'data' => $data
        ];

        $jsonPayload = json_encode($payload);

        foreach ($endpoints as $ep) {
            $subscribedEvents = json_decode($ep['events'], true) ?: [];
            
            // Check if endpoint is subscribed to this event
            if (!in_array($eventType, $subscribedEvents) && !in_array('*', $subscribedEvents)) {
                continue;
            }

            // Generate HMAC SHA256 Signature
            $secret = $ep['secret'];
            $signatureHash = hash_hmac('sha256', "{$timestamp}.{$jsonPayload}", $secret);
            $signatureHeader = "t={$timestamp},v1={$signatureHash}";

            // Send Webhook HTTP POST
            self::send($ep['id'], $merchantId, $eventId, $eventType, $ep['url'], $jsonPayload, $signatureHeader, 1);
        }
    }

    /**
     * Perform HTTP dispatch attempt and log result
     */
    public static function send(int $endpointId, int $merchantId, string $eventId, string $eventType, string $url, string $jsonPayload, string $signatureHeader, int $attempt): void {
        $pdo = Database::getConnection();

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-Gazoma-Signature: ' . $signatureHeader,
                'X-Gazoma-Event-Id: ' . $eventId,
                'User-Agent: GazomaPay-Webhooks/1.0'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $responseBody = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $status = ($responseCode >= 200 && $responseCode < 300) ? 'delivered' : (($attempt < 4) ? 'retrying' : 'failed');
        
        // Exponential backoff retry calculation: 1m, 5m, 30m, 2h
        $backoffMinutes = [1 => 1, 2 => 5, 3 => 30, 4 => 120];
        $nextRetryAt = ($status === 'retrying') ? date('Y-m-d H:i:s', strtotime("+{$backoffMinutes[$attempt]} minutes")) : null;

        $stmt = $pdo->prepare("INSERT INTO webhook_logs (merchant_id, endpoint_id, event_id, event_type, payload, signature, response_code, response_body, attempt_count, next_retry_at, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $merchantId,
            $endpointId,
            $eventId,
            $eventType,
            $jsonPayload,
            $signatureHeader,
            $responseCode ?: 0,
            $responseBody ?: 'No response / Timeout',
            $attempt,
            $nextRetryAt,
            $status
        ]);
    }
}
