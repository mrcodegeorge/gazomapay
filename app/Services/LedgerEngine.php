<?php

require_once __DIR__ . '/../../config/database.php';

class LedgerEngine {

    /**
     * Get or create a specific ledger account for a merchant
     */
    public static function getAccount(int $merchantId, string $accountType): array {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM ledger_accounts WHERE merchant_id = ? AND account_type = ?");
        $stmt->execute([$merchantId, $accountType]);
        $acc = $stmt->fetch();

        if ($acc) {
            return $acc;
        }

        $accNum = 'ACC_' . strtoupper($accountType) . '_' . sprintf('%06d', $merchantId);
        $ins = $pdo->prepare("INSERT INTO ledger_accounts (merchant_id, account_number, account_type, currency, status) VALUES (?, ?, ?, 'GHS', 'active')");
        $ins->execute([$merchantId, $accNum, $accountType]);

        $stmt->execute([$merchantId, $accountType]);
        return $stmt->fetch();
    }

    /**
     * Record a payment event in double-entry financial ledger
     */
    public static function recordPayment(int $merchantId, string $txReference, float $grossAmount, float $feeAmount, float $netAmount, string $description = 'Customer payment'): int {
        $pdo = Database::getConnection();

        $escrowAcc = self::getAccount($merchantId, 'customer_escrow');
        $availAcc = self::getAccount($merchantId, 'merchant_available');
        $feeAcc = self::getAccount($merchantId, 'platform_fee');

        $eventId = 'evt_' . bin2hex(random_bytes(10));
        $ledgerRef = 'LTX_PMT_' . sprintf('%08d', rand(100000, 99999999));

        // Create Ledger Transaction
        $stmtTx = $pdo->prepare("INSERT INTO ledger_transactions (reference, event_id, event_type, merchant_id, description, status) VALUES (?, ?, 'payment.created', ?, ?, 'posted')");
        $stmtTx->execute([$ledgerRef, $eventId, $merchantId, $description]);
        $ledgerTxId = $pdo->lastInsertId();

        // 1. Debit Escrow (Incoming customer funds)
        self::createEntry($ledgerTxId, $escrowAcc['id'], $grossAmount, 0.00);

        // 2. Credit Merchant Available (Net earnings)
        self::createEntry($ledgerTxId, $availAcc['id'], 0.00, $netAmount);

        // 3. Credit Platform Fee (Platform revenue)
        if ($feeAmount > 0) {
            self::createEntry($ledgerTxId, $feeAcc['id'], 0.00, $feeAmount);
        }

        return $ledgerTxId;
    }

    /**
     * Record a refund reversal event in double-entry financial ledger
     */
    public static function recordRefund(int $merchantId, string $refundReference, float $refundGross, float $refundFee, float $refundNet, string $description = 'Payment refund reversal'): int {
        $pdo = Database::getConnection();

        $escrowAcc = self::getAccount($merchantId, 'customer_escrow');
        $availAcc = self::getAccount($merchantId, 'merchant_available');
        $feeAcc = self::getAccount($merchantId, 'platform_fee');

        $eventId = 'evt_' . bin2hex(random_bytes(10));
        $ledgerRef = 'LTX_RFD_' . sprintf('%08d', rand(100000, 99999999));

        $stmtTx = $pdo->prepare("INSERT INTO ledger_transactions (reference, event_id, event_type, merchant_id, description, status) VALUES (?, ?, 'payment.refunded', ?, ?, 'posted')");
        $stmtTx->execute([$ledgerRef, $eventId, $merchantId, $description]);
        $ledgerTxId = $pdo->lastInsertId();

        // 1. Debit Merchant Available (Deduct refund net)
        self::createEntry($ledgerTxId, $availAcc['id'], $refundNet, 0.00);

        // 2. Debit Platform Fee Reversal
        if ($refundFee > 0) {
            self::createEntry($ledgerTxId, $feeAcc['id'], $refundFee, 0.00);
        }

        // 3. Credit Escrow (Customer refund disbursement)
        self::createEntry($ledgerTxId, $escrowAcc['id'], 0.00, $refundGross);

        return $ledgerTxId;
    }

    /**
     * Record a settlement payout request & completion in double-entry ledger
     */
    public static function recordSettlementRequest(int $merchantId, string $settlementRef, float $grossAmount, string $description = 'Settlement payout requested'): int {
        $pdo = Database::getConnection();

        $availAcc = self::getAccount($merchantId, 'merchant_available');
        $pendingAcc = self::getAccount($merchantId, 'merchant_pending');

        $eventId = 'evt_' . bin2hex(random_bytes(10));
        $ledgerRef = 'LTX_SET_REQ_' . sprintf('%08d', rand(100000, 99999999));

        $stmtTx = $pdo->prepare("INSERT INTO ledger_transactions (reference, event_id, event_type, merchant_id, description, status) VALUES (?, ?, 'settlement.requested', ?, ?, 'posted')");
        $stmtTx->execute([$ledgerRef, $eventId, $merchantId, $description]);
        $ledgerTxId = $pdo->lastInsertId();

        // 1. Debit Available
        self::createEntry($ledgerTxId, $availAcc['id'], $grossAmount, 0.00);

        // 2. Credit Pending Settlement
        self::createEntry($ledgerTxId, $pendingAcc['id'], 0.00, $grossAmount);

        return $ledgerTxId;
    }

    public static function recordSettlementCompletion(int $merchantId, string $settlementRef, float $grossAmount, float $feeAmount, float $netAmount, string $description = 'Settlement payout completed'): int {
        $pdo = Database::getConnection();

        $pendingAcc = self::getAccount($merchantId, 'merchant_pending');
        $disburseAcc = self::getAccount($merchantId, 'bank_disbursement');
        $feeAcc = self::getAccount($merchantId, 'platform_fee');

        $eventId = 'evt_' . bin2hex(random_bytes(10));
        $ledgerRef = 'LTX_SET_CMP_' . sprintf('%08d', rand(100000, 99999999));

        $stmtTx = $pdo->prepare("INSERT INTO ledger_transactions (reference, event_id, event_type, merchant_id, description, status) VALUES (?, ?, 'settlement.completed', ?, ?, 'posted')");
        $stmtTx->execute([$ledgerRef, $eventId, $merchantId, $description]);
        $ledgerTxId = $pdo->lastInsertId();

        // 1. Debit Pending Settlement
        self::createEntry($ledgerTxId, $pendingAcc['id'], $grossAmount, 0.00);

        // 2. Credit Bank Disbursement (Net payout)
        self::createEntry($ledgerTxId, $disburseAcc['id'], 0.00, $netAmount);

        // 3. Credit Fee if applicable
        if ($feeAmount > 0) {
            self::createEntry($ledgerTxId, $feeAcc['id'], 0.00, $feeAmount);
        }

        return $ledgerTxId;
    }

    private static function createEntry(int $ledgerTxId, int $accountId, float $debit, float $credit): void {
        $pdo = Database::getConnection();

        // Calculate running balance for account
        $stmtBal = $pdo->prepare("SELECT COALESCE(SUM(credit_amount - debit_amount), 0) FROM ledger_entries WHERE account_id = ?");
        $stmtBal->execute([$accountId]);
        $currentBalance = (float)$stmtBal->fetchColumn();

        $newBalance = round($currentBalance + ($credit - $debit), 2);

        $stmt = $pdo->prepare("INSERT INTO ledger_entries (ledger_transaction_id, account_id, debit_amount, credit_amount, currency, balance_after) VALUES (?, ?, ?, ?, 'GHS', ?)");
        $stmt->execute([$ledgerTxId, $accountId, $debit, $credit, $newBalance]);
    }

    /**
     * Calculate merchant available balance directly from double-entry ledger
     */
    public static function getAvailableBalance(int $merchantId): float {
        $pdo = Database::getConnection();
        $availAcc = self::getAccount($merchantId, 'merchant_available');

        $stmt = $pdo->prepare("SELECT COALESCE(SUM(credit_amount - debit_amount), 0) FROM ledger_entries WHERE account_id = ?");
        $stmt->execute([$availAcc['id']]);
        return (float)$stmt->fetchColumn();
    }

    /**
     * Calculate merchant pending balance directly from double-entry ledger
     */
    public static function getPendingBalance(int $merchantId): float {
        $pdo = Database::getConnection();
        $pendingAcc = self::getAccount($merchantId, 'merchant_pending');

        $stmt = $pdo->prepare("SELECT COALESCE(SUM(credit_amount - debit_amount), 0) FROM ledger_entries WHERE account_id = ?");
        $stmt->execute([$pendingAcc['id']]);
        return (float)$stmt->fetchColumn();
    }

    /**
     * Calculate merchant total settled amount directly from double-entry ledger
     */
    public static function getSettledBalance(int $merchantId): float {
        $pdo = Database::getConnection();
        $disburseAcc = self::getAccount($merchantId, 'bank_disbursement');

        $stmt = $pdo->prepare("SELECT COALESCE(SUM(credit_amount - debit_amount), 0) FROM ledger_entries WHERE account_id = ?");
        $stmt->execute([$disburseAcc['id']]);
        return (float)$stmt->fetchColumn();
    }
}
