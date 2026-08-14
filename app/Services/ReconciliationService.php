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
        $stmtTx = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as gross_tx, COALESCE(SUM(fee), 0) as fee_tx, COALESCE(SUM(net_amount), 0) as net_tx FROM transactions WHERE merchant_id = ? AND status = 'successful'");
        $stmtTx->execute([$merchantId]);
        $txTotals = $stmtTx->fetch();

        // Ledger Available Balance
        $ledgerAvail = LedgerEngine::getAvailableBalance($merchantId);
        $ledgerPending = LedgerEngine::getPendingBalance($merchantId);
        $ledgerSettled = LedgerEngine::getSettledBalance($merchantId);

        // Merchant Record Stored Balance
        $stmtMch = $pdo->prepare("SELECT available_balance, pending_balance, settled_balance FROM merchants WHERE id = ?");
        $stmtMch->execute([$merchantId]);
        $mchRecord = $stmtMch->fetch();

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

        return [
            'status' => $status,
            'timestamp' => date('Y-m-d H:i:s'),
            'merchant_id' => $merchantId,
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
