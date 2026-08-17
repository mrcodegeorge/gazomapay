<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/env.php';

class OutboundWebhookService {

    /**
     * Dispatch an outbound event notification to all active merchant webhook endpoints.
     */
    public static function dispatch(int $merchantId, string $eventType, array $eventData): void {
        $pdo = Database::getConnection();

        // 1. Fetch Merchant Webhook Endpoints
        $stmtEp = $pdo->prepare("SELECT * FROM webhook_endpoints WHERE merchant_id = ? AND status = 'active'");
        $stmtEp->execute([$merchantId]);
        $endpoints = $stmtEp->fetchAll(PDO::FETCH_ASSOC);

        if (empty($endpoints)) {
            return;
        }

        $publicId = 'evt_' . bin2hex(random_bytes(10));
        $secret = Env::get('GAZOMA_WEBHOOK_SECRET', 'whsec_9a8b7c6d5e4f3a2b1c');

        $payloadArray = [
            'id' => $publicId,
            'object' => 'event',
            'event_type' => $eventType,
            'data' => $eventData,
            'created_at' => date('c')
        ];

        $payloadJson = json_encode($payloadArray, JSON_UNESCAPED_SLASHES);
        $signature = hash_hmac('sha256', $payloadJson, $secret);

        foreach ($endpoints as $ep) {
            $targetUrl = $ep['url'];

            // Store in Outbound Queue
            $stmtIns = $pdo->prepare("
                INSERT INTO outbound_webhooks (public_id, merchant_id, event_type, payload, target_url, delivery_status, attempts) 
                VALUES (?, ?, ?, ?, ?, 'pending', 0)
            ");
            $stmtIns->execute([$publicId, $merchantId, $eventType, $payloadJson, $targetUrl]);
            $outboundId = (int)$pdo->lastInsertId();

            // Execute Immediate Dispatch Request
            self::deliver($outboundId, $targetUrl, $signature, $payloadJson);
        }
    }

    private static function deliver(int $outboundId, string $url, string $signature, string $jsonPayload): bool {
        $pdo = Database::getConnection();

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-Gazoma-Signature: ' . $signature,
                'User-Agent: Gazoma-Pay-Webhook/1.0'
            ],
            CURLOPT_TIMEOUT => 5
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        $success = ($statusCode >= 200 && $statusCode < 300);
        $deliveryStatus = $success ? 'delivered' : 'failed';

        $stmtUpd = $pdo->prepare("
            UPDATE outbound_webhooks 
            SET response_status = ?, response_body = ?, delivery_status = ?, attempts = attempts + 1 
            WHERE id = ?
        ");
        $stmtUpd->execute([$statusCode ?: 500, substr($response ?: $err, 0, 500), $deliveryStatus, $outboundId]);

        return $success;
    }
}
