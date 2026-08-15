<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/Format.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/CsrfMiddleware.php';
require_once __DIR__ . '/../Services/AuditLogger.php';
require_once __DIR__ . '/../Services/LedgerEngine.php';
require_once __DIR__ . '/../../config/database.php';

class AdminController {

    public function index(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();

        // 1. System High-Level KPI Metrics
        $stmtVol = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE status = 'successful'");
        $totalSystemVolume = (float)$stmtVol->fetchColumn();

        $stmtFees = $pdo->query("SELECT COALESCE(SUM(fee), 0) FROM transactions WHERE status = 'successful'");
        $totalPlatformRevenue = (float)$stmtFees->fetchColumn();

        $stmtMchCount = $pdo->query("SELECT COUNT(*) FROM merchants");
        $totalMerchants = (int)$stmtMchCount->fetchColumn();

        $stmtPendingSettlements = $pdo->query("SELECT COUNT(*) FROM settlements WHERE status = 'pending'");
        $pendingPayoutCount = (int)$stmtPendingSettlements->fetchColumn();

        // 2. Merchants Directory List
        $stmtMch = $pdo->query("SELECT * FROM merchants ORDER BY created_at DESC");
        $merchants = $stmtMch->fetchAll(PDO::FETCH_ASSOC);

        // 3. Pending Platform Settlement Requests
        $stmtSet = $pdo->query("
            SELECT s.*, m.name as merchant_name, m.email as merchant_email 
            FROM settlements s 
            JOIN merchants m ON s.merchant_id = m.id 
            ORDER BY s.created_at DESC
        ");
        $settlements = $stmtSet->fetchAll(PDO::FETCH_ASSOC);

        // 4. Global System Audit Trail Logs
        $stmtLogs = $pdo->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 25");
        $systemLogs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

        View::render('admin/index', [
            'pageTitle' => 'Super Admin Platform Command Center',
            'pageSubtitle' => 'Real-time multi-tenant platform oversight, KYB approvals, settlement processing, and financial audit logs.',
            'totalSystemVolume' => $totalSystemVolume,
            'totalPlatformRevenue' => $totalPlatformRevenue,
            'totalMerchants' => $totalMerchants,
            'pendingPayoutCount' => $pendingPayoutCount,
            'merchants' => $merchants,
            'settlements' => $settlements,
            'systemLogs' => $systemLogs
        ]);
    }

    public function approveKyc(string $id): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("UPDATE merchants SET kyc_status = 'approved', account_status = 'active', status = 'active' WHERE id = ?");
        $stmt->execute([(int)$id]);

        AuditLogger::log('admin.kyc_approved', "Approved KYB verification & activated merchant #{$id}");
        Response::setFlash('success', "Merchant #{$id} KYB verification approved!");

        Response::redirect('/admin');
    }

    public function toggleStatus(string $id): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();

        $stmtFetch = $pdo->prepare("SELECT account_status FROM merchants WHERE id = ?");
        $stmtFetch->execute([(int)$id]);
        $current = $stmtFetch->fetchColumn();

        if ($current) {
            $newStatus = ($current === 'active') ? 'suspended' : 'active';
            $stmtUpd = $pdo->prepare("UPDATE merchants SET account_status = ?, status = ? WHERE id = ?");
            $stmtUpd->execute([$newStatus, $newStatus, (int)$id]);

            AuditLogger::log('admin.merchant_status_updated', "Updated merchant #{$id} status to {$newStatus}");
            Response::setFlash('success', "Merchant #{$id} status set to {$newStatus}.");
        }

        Response::redirect('/admin');
    }

    public function processSettlement(string $id): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();

        $stmtSet = $pdo->prepare("SELECT * FROM settlements WHERE id = ?");
        $stmtSet->execute([(int)$id]);
        $s = $stmtSet->fetch(PDO::FETCH_ASSOC);

        if ($s && $s['status'] === 'pending') {
            // Execute double-entry financial ledger settlement completion
            LedgerEngine::recordSettlementCompletion(
                (int)$s['merchant_id'],
                $s['reference'],
                (float)$s['gross_amount'],
                (float)$s['fee'],
                (float)$s['net_amount']
            );

            // Update settlement status to completed
            $stmtUpd = $pdo->prepare("UPDATE settlements SET status = 'completed', processed_at = NOW() WHERE id = ?");
            $stmtUpd->execute([(int)$id]);

            // Sync merchant balances
            $realAvail = LedgerEngine::getAvailableBalance((int)$s['merchant_id']);
            $realPending = LedgerEngine::getPendingBalance((int)$s['merchant_id']);
            $realSettled = LedgerEngine::getSettledBalance((int)$s['merchant_id']);

            $stmtBal = $pdo->prepare("UPDATE merchants SET available_balance = ?, pending_balance = ?, settled_balance = ? WHERE id = ?");
            $stmtBal->execute([$realAvail, $realPending, $realSettled, (int)$s['merchant_id']]);

            AuditLogger::log('admin.settlement_processed', "Approved & processed settlement {$s['reference']} for GH₵ {$s['net_amount']}");
            Response::setFlash('success', "Settlement {$s['reference']} (GH₵ " . number_format($s['net_amount'], 2) . ") completed & ledger balanced!");
        }

        Response::redirect('/admin');
    }
}
