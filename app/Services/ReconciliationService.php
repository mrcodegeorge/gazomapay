<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/LedgerEngine.php';

class ReconciliationService {

    /**
     * Audit merchant financial transactions against immutable double-entry ledger
     */
    public static function auditMerchant(int $merchantId): array {
        $pdo = Database::getConnection();

        // 1. Transaction Total vs Ledger Total
        $stmtTx = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as gross_tx, COALESCE(SUM(fee), 0) as fee_tx, COALESCE(SUM(net_amount), 0) as net_tx FROM transactions WHERE merchant_id = ? AND status IN ('successful', 'completed')");
        $stmtTx->execute([$merchantId]);
        $txTotals = $stmtTx->fetch(PDO::FETCH_ASSOC);

        // Ledger Available Balance
        $ledgerAvail = LedgerEngine::getAvailableBalance($merchantId);
        $ledgerPending = LedgerEngine::getPendingBalance($merchantId);
        $ledgerSettled = LedgerEngine::getSettledBalance($merchantId);

        // Merchant Record Stored Balance
        $stmtMch = $pdo->prepare("SELECT available_balance, pending_balance, settled_balance FROM merchants WHERE id = ?");
        $stmtMch->execute([$merchantId]);
        $mchRecord = $stmtMch->fetch(PDO::FETCH_ASSOC);

        $discrepancyAvail = abs((float)$mchRecord['available_balance'] - $ledgerAvail);
        $discrepancyPending = abs((float)$mchRecord['pending_balance'] - $ledgerPending);

        $status = 'PASS';
        $issues = [];

        if ($discrepancyAvail > 0.01) {
            $status = 'WARNING';
            $issues[] = "Stored available balance (GH₵ {$mchRecord['available_balance']}) differs from ledger balance (GH₵ {$ledgerAvail}) by GH₵ {$discrepancyAvail}";
        }

        if ($discrepancyPending > 0.01) {
            $status = 'WARNING';
            $issues[] = "Stored pending balance (GH₵ {$mchRecord['pending_balance']}) differs from ledger pending balance (GH₵ {$ledgerPending})";
        }

        // Persist Reconciliation Run
        $runCode = 'REC_' . date('Ymd_His') . '_' . rand(100, 999);
        $stmtRun = $pdo->prepare("INSERT INTO reconciliation_runs (run_code, started_by, period_start, period_end, total_transactions, total_gross_amount, total_ledger_amount, discrepancy_count, discrepancy_amount, status) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?)");
        $stmtRun->execute([
            $runCode,
            'system',
            date('Y-m-d H:i:s', strtotime('-30 days')),
            1,
            (float)$txTotals['gross_tx'],
            $ledgerAvail,
            count($issues),
            $discrepancyAvail,
            count($issues) > 0 ? 'exceptions_found' : 'completed'
        ]);
        $runId = (int)$pdo->lastInsertId();

        foreach ($issues as $issueText) {
            $stmtItem = $pdo->prepare("INSERT INTO reconciliation_items (reconciliation_run_id, reference, discrepancy_type, expected_amount, actual_amount, details, resolution_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtItem->execute([
                $runId,
                $runCode,
                'amount_mismatch',
                $ledgerAvail,
                (float)$mchRecord['available_balance'],
                $issueText,
                'unresolved'
            ]);
        }

        return [
            'status' => $status,
            'timestamp' => date('Y-m-d H:i:s'),
            'merchant_id' => $merchantId,
            'run_code' => $runCode,
            'metrics' => [
                'transaction_gross_volume' => (float)$txTotals['gross_tx'],
                'transaction_fees' => (float)$txTotals['fee_tx'],
                'transaction_net_volume' => (float)$txTotals['net_tx'],
                'ledger_available_balance' => $ledgerAvail,
                'ledger_pending_balance' => $ledgerPending,
                'ledger_settled_balance' => $ledgerSettled,
                'stored_available_balance' => (float)$mchRecord['available_balance']
            ],
            'issues' => $issues
        ];
    }
}
