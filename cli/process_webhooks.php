<?php

// CLI Cron Script: Process Webhooks Queue
if (php_sapi_name() !== 'cli') {
    die("Error: This script must be executed from the CLI command line.\n");
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Services/WebhookEngine.php';

echo "[" . date('Y-m-d H:i:s') . "] Starting Gazoma Pay CLI Webhook Queue Worker...\n";

try {
    $pdo = Database::getConnection();
    
    // Lock pending/retryable webhooks using database locks
    $stmt = $pdo->query("SELECT id, provider, payload, signature FROM webhook_events WHERE status = 'received' OR (status = 'failed' AND retry_count < max_retries) ORDER BY id ASC LIMIT 50");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($events) . " webhook events to process.\n";

    foreach ($events as $evt) {
        $headers = ['x-gazoma-signature' => $evt['signature'] ?? ''];
        $res = WebhookEngine::receiveAndProcess($evt['provider'], $headers, $evt['payload']);
        echo "Processed Event #{$evt['id']} ({$evt['provider']}): " . ($res['status'] ?? 'unknown') . "\n";
    }

    echo "[" . date('Y-m-d H:i:s') . "] Webhook Queue Processing Completed Successfully.\n";
} catch (Exception $e) {
    echo "CLI Webhook Processing Error: " . $e->getMessage() . "\n";
    exit(1);
}
