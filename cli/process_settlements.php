<?php

// CLI Cron Script: Process Merchant Settlements
if (php_sapi_name() !== 'cli') {
    die("Error: This script must be executed from the CLI command line.\n");
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Services/LedgerEngine.php';

echo "[" . date('Y-m-d H:i:s') . "] Starting Gazoma Pay Settlement Cron Worker...\n";

try {
    $pdo = Database::getConnection();

    // Query pending settlements
    $stmt = $pdo->query("SELECT * FROM settlements WHERE status = 'pending' LIMIT 20");
    $settlements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($settlements) . " pending settlements.\n";

    foreach ($settlements as $st) {
        $amt = (float)($st['amount'] ?? ($st['net_amount'] ?? 0));
        $pdo->prepare("UPDATE settlements SET status = 'completed', updated_at = NOW() WHERE id = ?")->execute([$st['id']]);
        echo "Settlement #{$st['id']} (GH₵ {$amt}) marked COMPLETED.\n";
    }

    echo "[" . date('Y-m-d H:i:s') . "] Settlement Processing Completed.\n";
} catch (Exception $e) {
    echo "CLI Settlement Error: " . $e->getMessage() . "\n";
    exit(1);
}
