<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/LedgerEngine.php';
require_once __DIR__ . '/AuditLogger.php';

class WebhookEngine {

    /**
     * Process an incoming webhook event payload through the secure pipeline.
     */
    public static function receiveAndProcess(string $provider, array $headers, string $rawBody): array {
        $pdo = Database::getConnection();

        // 1. Verify HMAC Signature
        $signatureValid = self::verifySignature($provider, $headers, $rawBody);
        if (!$signatureValid) {
            AuditLogger::log('webhook_signature_failed', 'Invalid HMAC signature', [
                'provider' => $provider,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
            return [
                'success' => false,
                'status' => 'unauthorized',
                'message' => 'Invalid HMAC webhook signature'
            ];
        }

        $payload = json_decode($rawBody, true) ?? [];
        $eventId = self::extractEventId($provider, $payload, $headers);
        $eventType = self::extractEventType($provider, $payload);
        $reference = self::extractReference($provider, $payload);

        // 2. Check Deduplication
        $stmtCheck = $pdo->prepare("SELECT id, status FROM webhook_events WHERE provider = ? AND event_id = ?");
        $stmtCheck->execute([$provider, $eventId]);
        $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existing && $existing['status'] === 'processed') {
            return [
                'success' => true,
                'status' => 'duplicate',
                'message' => 'Webhook event already processed successfully'
            ];
        }

        // 3. Store or Update Event Record
        if (!$existing) {
            $stmtIns = $pdo->prepare("INSERT INTO webhook_events (uuid, provider, event_id, event_type, signature, payload, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtIns->execute([
                'wh_evt_' . bin2hex(random_bytes(8)),
                $provider,
                $eventId,
                $eventType,
                $headers['x-gazoma-signature'] ?? ($headers['x-paystack-signature'] ?? ''),
                $rawBody,
                'processing'
            ]);
            $webhookDbId = (int)$pdo->lastInsertId();
        } else {
            $webhookDbId = (int)$existing['id'];
            $pdo->prepare("UPDATE webhook_events SET status = 'processing' WHERE id = ?")->execute([$webhookDbId]);
        }

        // 4. Process Event & Update Transaction & Ledger
        try {
            $processResult = self::dispatchBusinessLogic($reference, $eventType, $payload);

            $stmtMark = $pdo->prepare("UPDATE webhook_events SET status = 'processed', processed_at = NOW() WHERE id = ?");
            $stmtMark->execute([$webhookDbId]);

            return [
                'success' => true,
                'status' => 'processed',
                'event_id' => $eventId,
                'result' => $processResult
            ];
        } catch (Exception $e) {
            $stmtErr = $pdo->prepare("UPDATE webhook_events SET status = 'failed', retry_count = retry_count + 1, last_error = ? WHERE id = ?");
            $stmtErr->execute([$e->getMessage(), $webhookDbId]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Webhook processing exception: ' . $e->getMessage()
            ];
        }
    }

    public static function verifySignature(string $provider, array $headers, string $rawBody): bool {
        $secret = Env::get('GAZOMA_WEBHOOK_SECRET', 'whsec_9a8b7c6d5e4f3a2b1c');
        $signature = $headers['x-gazoma-signature'] ?? ($headers['X-Gazoma-Signature'] ?? ($headers['x-paystack-signature'] ?? ''));

        if (empty($signature)) {
            // For local sandbox requests without signature header, allow if in local sandbox mode
            return Env::get('GAZOMA_PAYMENT_MODE') === 'sandbox';
        }

        $calculated = hash_hmac('sha256', $rawBody, $secret);
        return hash_equals($calculated, $signature);
    }

    private static function extractEventId(string $provider, array $payload, array $headers): string {
        if (!empty($payload['event_id'])) return $payload['event_id'];
        if (!empty($payload['id'])) return (string)$payload['id'];
        if (!empty($headers['x-event-id'])) return $headers['x-event-id'];
        return 'evt_' . md5(json_encode($payload));
    }

    private static function extractEventType(string $provider, array $payload): string {
        return $payload['event'] ?? ($payload['event_type'] ?? 'charge.success');
    }

    private static function extractReference(string $provider, array $payload): string {
        return $payload['reference'] ?? ($payload['data']['reference'] ?? ($payload['clientReference'] ?? ''));
    }

    private static function dispatchBusinessLogic(string $reference, string $eventType, array $payload): array {
        if (empty($reference)) {
            return ['note' => 'No reference found in payload'];
        }

        $pdo = Database::getConnection();
        $stmtTx = $pdo->prepare("SELECT * FROM transactions WHERE reference = ?");
        $stmtTx->execute([$reference]);
        $tx = $stmtTx->fetch(PDO::FETCH_ASSOC);

        if (!$tx) {
            return ['note' => 'Transaction reference not found in local DB'];
        }

        if ($tx['status'] !== 'successful' && strpos($eventType, 'success') !== false) {
            $pdo->prepare("UPDATE transactions SET status = 'successful', updated_at = NOW() WHERE id = ?")->execute([$tx['id']]);
            
            // Check if Ledger Entry already exists for this transaction
            $stmtCheckL = $pdo->prepare("SELECT COUNT(*) FROM ledger_transactions WHERE merchant_id = ? AND (description LIKE ? OR reference LIKE ?)");
            $stmtCheckL->execute([$tx['merchant_id'], "%{$tx['reference']}%", "%{$tx['reference']}%"]);
            $hasLedger = (int)$stmtCheckL->fetchColumn() > 0;

            if (!$hasLedger) {
                $feeAmt = (float)($tx['fee'] ?? ($tx['fee_amount'] ?? 0));
                LedgerEngine::recordPayment(
                    (int)$tx['merchant_id'],
                    (string)$tx['reference'],
                    (float)$tx['amount'],
                    $feeAmt,
                    (float)$tx['net_amount'],
                    'Webhook charge verification'
                );
                $pdo->prepare("UPDATE merchants SET available_balance = available_balance + ? WHERE id = ?")->execute([$tx['net_amount'], $tx['merchant_id']]);
            }
        }

        return ['transaction_id' => $tx['id'], 'status' => 'updated'];
    }
}
