<?php

// CLI Cron Script: Process Automated Financial Reconciliation
if (php_sapi_name() !== 'cli') {
    die("Error: This script must be executed from the CLI command line.\n");
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Services/ReconciliationService.php';

echo "[" . date('Y-m-d H:i:s') . "] Starting Automated Financial Reconciliation Worker...\n";

try {
    $pdo = Database::getConnection();
    $stmtMch = $pdo->query("SELECT id, name FROM merchants WHERE status = 'active'");
    $merchants = $stmtMch->fetchAll(PDO::FETCH_ASSOC);

    foreach ($merchants as $mch) {
        $audit = ReconciliationService::auditMerchant((int)$mch['id']);
        echo "Merchant #{$mch['id']} ({$mch['name']}): Status = {$audit['status']} | Discrepancies = " . count($audit['issues']) . "\n";
    }

    echo "[" . date('Y-m-d H:i:s') . "] Financial Reconciliation Completed.\n";
} catch (Exception $e) {
    echo "CLI Reconciliation Error: " . $e->getMessage() . "\n";
    exit(1);
}
