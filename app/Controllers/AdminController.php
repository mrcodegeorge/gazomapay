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

    /**
     * Menu 1: Platform Overview Dashboard
     * Route: GET /admin
     */
    public function index(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();

        $stmtVol = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE status = 'successful'");
        $totalSystemVolume = (float)$stmtVol->fetchColumn();

        $stmtFees = $pdo->query("SELECT COALESCE(SUM(fee), 0) FROM transactions WHERE status = 'successful'");
        $totalPlatformRevenue = (float)$stmtFees->fetchColumn();

        $stmtMchCount = $pdo->query("SELECT COUNT(*) FROM merchants");
        $totalMerchants = (int)$stmtMchCount->fetchColumn();

        $stmtPendingSettlements = $pdo->query("SELECT COUNT(*) FROM settlements WHERE status = 'pending'");
        $pendingPayoutCount = (int)$stmtPendingSettlements->fetchColumn();

        $stmtDisputesCount = $pdo->query("SELECT COUNT(*) FROM disputes WHERE status IN ('needs_response', 'under_review')");
        $activeDisputesCount = (int)$stmtDisputesCount->fetchColumn();

        $stmtMch = $pdo->query("SELECT * FROM merchants ORDER BY created_at DESC LIMIT 10");
        $merchants = $stmtMch->fetchAll(PDO::FETCH_ASSOC);

        $stmtSet = $pdo->query("
            SELECT s.*, m.name as merchant_name, m.email as merchant_email 
            FROM settlements s 
            JOIN merchants m ON s.merchant_id = m.id 
            ORDER BY s.created_at DESC LIMIT 10
        ");
        $settlements = $stmtSet->fetchAll(PDO::FETCH_ASSOC);

        $stmtDsp = $pdo->query("
            SELECT d.*, m.name as merchant_name, t.reference as tx_reference 
            FROM disputes d 
            JOIN merchants m ON d.merchant_id = m.id 
            JOIN transactions t ON d.transaction_id = t.id 
            ORDER BY d.created_at DESC LIMIT 10
        ");
        $disputes = $stmtDsp->fetchAll(PDO::FETCH_ASSOC);

        $platformSettings = $this->getPlatformSettingsData();

        $stmtLogs = $pdo->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 15");
        $systemLogs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

        View::render('admin/index', [
            'pageTitle' => 'Super Admin Platform Console',
            'pageSubtitle' => 'Full-stack platform control, KYB approvals, custom merchant fee tiers, balance adjustments, and dispute overrides.',
            'totalSystemVolume' => $totalSystemVolume,
            'totalPlatformRevenue' => $totalPlatformRevenue,
            'totalMerchants' => $totalMerchants,
            'pendingPayoutCount' => $pendingPayoutCount,
            'activeDisputesCount' => $activeDisputesCount,
            'merchants' => $merchants,
            'settlements' => $settlements,
            'disputes' => $disputes,
            'platformSettings' => $platformSettings,
            'systemLogs' => $systemLogs
        ], 'admin');
    }

    /**
     * Menu 2: Dedicated Merchants & KYB Clearance Center
     * Route: GET /admin/merchants
     */
    public function merchantsPage(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();

        $kycFilter = $_GET['kyc_status'] ?? 'all';
        $search = trim($_GET['search'] ?? '');

        $sql = "SELECT * FROM merchants WHERE 1=1";
        $params = [];

        if ($kycFilter !== 'all') {
            $sql .= " AND kyc_status = ?";
            $params[] = $kycFilter;
        }

        if ($search !== '') {
            $sql .= " AND (name LIKE ? OR email LIKE ? OR merchant_id LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $merchants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        View::render('admin/merchants', [
            'pageTitle' => 'Merchants Directory & KYB Clearance',
            'pageSubtitle' => 'Review business registration documents, grant KYB approvals, adjust merchant balances, and set custom fee structures.',
            'merchants' => $merchants,
            'kycFilter' => $kycFilter,
            'search' => $search
        ], 'admin');
    }

    /**
     * Menu 3: Dedicated Settlement Clearances Page
     * Route: GET /admin/settlements
     */
    public function settlementsPage(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();

        $statusFilter = $_GET['status'] ?? 'all';
        $sql = "
            SELECT s.*, m.name as merchant_name, m.email as merchant_email 
            FROM settlements s 
            JOIN merchants m ON s.merchant_id = m.id 
            WHERE 1=1
        ";
        $params = [];

        if ($statusFilter !== 'all') {
            $sql .= " AND s.status = ?";
            $params[] = $statusFilter;
        }

        $sql .= " ORDER BY s.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $settlements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        View::render('admin/settlements', [
            'pageTitle' => 'Platform Settlement Payout Clearances',
            'pageSubtitle' => 'Review merchant payout requests and execute double-entry ledger settlement releases.',
            'settlements' => $settlements,
            'statusFilter' => $statusFilter
        ], 'admin');
    }

    /**
     * Menu 4: Dedicated Platform Disputes Page
     * Route: GET /admin/disputes
     */
    public function disputesPage(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();

        $stmtDsp = $pdo->query("
            SELECT d.*, m.name as merchant_name, t.reference as tx_reference, c.name as customer_name 
            FROM disputes d 
            JOIN merchants m ON d.merchant_id = m.id 
            JOIN transactions t ON d.transaction_id = t.id 
            LEFT JOIN customers c ON d.customer_id = c.id
            ORDER BY d.created_at DESC
        ");
        $disputes = $stmtDsp->fetchAll(PDO::FETCH_ASSOC);

        View::render('admin/disputes', [
            'pageTitle' => 'Global Platform Disputes & Chargebacks',
            'pageSubtitle' => 'System-wide customer disputes requiring superadmin override resolution.',
            'disputes' => $disputes
        ], 'admin');
    }

    /**
     * Menu 5: Dedicated Gateway & Fee Engine Settings Page
     * Route: GET /admin/settings
     */
    public function settingsPage(): void {
        AuthMiddleware::handle();
        $platformSettings = $this->getPlatformSettingsData();

        View::render('admin/settings', [
            'pageTitle' => 'Gateway & Fee Engine Configuration',
            'pageSubtitle' => 'Update global processing fee rates, emergency maintenance mode, and payment gateway drivers.',
            'platformSettings' => $platformSettings
        ], 'admin');
    }

    /**
     * Menu 6: Dedicated System Audit Trail Page
     * Route: GET /admin/audit-logs
     */
    public function auditLogsPage(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();

        $search = trim($_GET['search'] ?? '');
        $sql = "SELECT * FROM audit_logs WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (action LIKE ? OR user_email LIKE ? OR ip_address LIKE ? OR details LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY created_at DESC LIMIT 100";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $systemLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        View::render('admin/audit', [
            'pageTitle' => 'Global Immutable System Audit Trail',
            'pageSubtitle' => 'Complete security logging for platform actions, balance adjustments, and operator clearances.',
            'systemLogs' => $systemLogs,
            'search' => $search
        ], 'admin');
    }

    private function getPlatformSettingsData(): array {
        $pdo = Database::getConnection();
        $stmtSettings = $pdo->query("SELECT setting_key, setting_value FROM platform_settings");
        $rawSettings = $stmtSettings->fetchAll(PDO::FETCH_KEY_PAIR);

        return [
            'platform_fee_percent' => $rawSettings['platform_fee_percent'] ?? '1.50',
            'platform_fee_flat' => $rawSettings['platform_fee_flat'] ?? '0.50',
            'maintenance_mode' => $rawSettings['maintenance_mode'] ?? '0',
            'gateway_driver' => $rawSettings['gateway_driver'] ?? 'Sandbox Payment Gateway'
        ];
    }

    public function approveKyc(string $id): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("UPDATE merchants SET kyc_status = 'approved', account_status = 'active', status = 'active' WHERE id = ?");
        $stmt->execute([(int)$id]);

        AuditLogger::log('admin.kyc_approved', "Approved KYB verification & activated merchant #{$id}");
        Response::setFlash('success', "Merchant #{$id} KYB verification approved!");

        Response::redirect($_SERVER['HTTP_REFERER'] ?? '/admin/merchants');
    }

    public function rejectKyc(string $id): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("UPDATE merchants SET kyc_status = 'rejected', account_status = 'restricted' WHERE id = ?");
        $stmt->execute([(int)$id]);

        AuditLogger::log('admin.kyc_rejected', "Rejected KYB verification for merchant #{$id}");
        Response::setFlash('error', "Merchant #{$id} KYB verification rejected.");

        Response::redirect($_SERVER['HTTP_REFERER'] ?? '/admin/merchants');
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

        Response::redirect($_SERVER['HTTP_REFERER'] ?? '/admin/merchants');
    }

    public function updateMerchantFee(string $id): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $pdo = Database::getConnection();
        $feePct = !empty($_POST['custom_fee_percentage']) ? (float)$_POST['custom_fee_percentage'] : null;
        $feeFlat = !empty($_POST['custom_fee_flat']) ? (float)$_POST['custom_fee_flat'] : null;

        $stmt = $pdo->prepare("UPDATE merchants SET custom_fee_percentage = ?, custom_fee_flat = ? WHERE id = ?");
        $stmt->execute([$feePct, $feeFlat, (int)$id]);

        AuditLogger::log('admin.merchant_fee_updated', "Updated custom fee rate for merchant #{$id} ({$feePct}% + GH₵ {$feeFlat})");
        Response::setFlash('success', "Custom fee rate saved for merchant #{$id}!");

        Response::redirect($_SERVER['HTTP_REFERER'] ?? '/admin/merchants');
    }

    public function adjustMerchantBalance(string $id): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $pdo = Database::getConnection();
        $adjustmentType = $_POST['adjustment_type'] ?? 'credit';
        $amount = (float)($_POST['amount'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Superadmin Manual Balance Adjustment');

        if ($amount <= 0) {
            Response::setFlash('error', 'Please enter a positive adjustment amount.');
            Response::redirect($_SERVER['HTTP_REFERER'] ?? '/admin/merchants');
        }

        $stmtFetch = $pdo->prepare("SELECT available_balance FROM merchants WHERE id = ?");
        $stmtFetch->execute([(int)$id]);
        $currentBal = (float)$stmtFetch->fetchColumn();

        $newBal = ($adjustmentType === 'credit') ? ($currentBal + $amount) : max(0, $currentBal - $amount);

        $stmtUpd = $pdo->prepare("UPDATE merchants SET available_balance = ? WHERE id = ?");
        $stmtUpd->execute([$newBal, (int)$id]);

        AuditLogger::log('admin.balance_adjusted', "Manual {$adjustmentType} of GH₵ {$amount} for merchant #{$id}. Reason: {$reason}");
        Response::setFlash('success', "Merchant #{$id} balance adjusted successfully by GH₵ {$amount} ({$adjustmentType}).");

        Response::redirect($_SERVER['HTTP_REFERER'] ?? '/admin/merchants');
    }

    public function processSettlement(string $id): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();

        $stmtSet = $pdo->prepare("SELECT * FROM settlements WHERE id = ?");
        $stmtSet->execute([(int)$id]);
        $s = $stmtSet->fetch(PDO::FETCH_ASSOC);

        if ($s && $s['status'] === 'pending') {
            LedgerEngine::recordSettlementCompletion(
                (int)$s['merchant_id'],
                $s['reference'],
                (float)$s['gross_amount'],
                (float)$s['fee'],
                (float)$s['net_amount']
            );

            $stmtUpd = $pdo->prepare("UPDATE settlements SET status = 'completed', processed_at = NOW() WHERE id = ?");
            $stmtUpd->execute([(int)$id]);

            $realAvail = LedgerEngine::getAvailableBalance((int)$s['merchant_id']);
            $realPending = LedgerEngine::getPendingBalance((int)$s['merchant_id']);
            $realSettled = LedgerEngine::getSettledBalance((int)$s['merchant_id']);

            $stmtBal = $pdo->prepare("UPDATE merchants SET available_balance = ?, pending_balance = ?, settled_balance = ? WHERE id = ?");
            $stmtBal->execute([$realAvail, $realPending, $realSettled, (int)$s['merchant_id']]);

            AuditLogger::log('admin.settlement_processed', "Approved & processed settlement {$s['reference']} for GH₵ {$s['net_amount']}");
            Response::setFlash('success', "Settlement {$s['reference']} (GH₵ " . number_format($s['net_amount'], 2) . ") completed & ledger balanced!");
        }

        Response::redirect($_SERVER['HTTP_REFERER'] ?? '/admin/settlements');
    }

    public function updatePlatformSettings(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $pdo = Database::getConnection();
        $feePct = trim($_POST['platform_fee_percent'] ?? '1.50');
        $feeFlat = trim($_POST['platform_fee_flat'] ?? '0.50');
        $maintMode = !empty($_POST['maintenance_mode']) ? '1' : '0';

        $stmtUpsert = $pdo->prepare("INSERT INTO platform_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $stmtUpsert->execute(['platform_fee_percent', $feePct]);
        $stmtUpsert->execute(['platform_fee_flat', $feeFlat]);
        $stmtUpsert->execute(['maintenance_mode', $maintMode]);

        AuditLogger::log('admin.platform_settings_updated', "Updated global platform settings (Fee: {$feePct}% + GH₵ {$feeFlat}, Maint: {$maintMode})");
        Response::setFlash('success', 'Global platform settings updated successfully!');

        Response::redirect($_SERVER['HTTP_REFERER'] ?? '/admin/settings');
    }

    public function resolveDispute(string $id): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $status = $_POST['status'] ?? 'won';

        $stmtUpd = $pdo->prepare("UPDATE disputes SET status = ?, resolved_at = NOW() WHERE id = ?");
        $stmtUpd->execute([$status, (int)$id]);

        AuditLogger::log('admin.dispute_resolved', "Super Admin set dispute #{$id} status to {$status}");
        Response::setFlash('success', "Dispute #{$id} status updated to {$status}.");

        Response::redirect($_SERVER['HTTP_REFERER'] ?? '/admin/disputes');
    }
}
