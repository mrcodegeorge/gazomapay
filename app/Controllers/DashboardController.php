<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Format.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../config/database.php';

class DashboardController {
    public function index(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();
        $user = Auth::user();
        $env = $user['environment'] ?? 'test';
        $livemode = ($env === 'live') ? 1 : 0;

        // 1. Available Balance & Onboarding Status
        $stmtMch = $pdo->prepare("SELECT available_balance, pending_balance, settled_balance, onboarding_completed, onboarding_step FROM merchants WHERE id = ?");
        $stmtMch->execute([$merchantId]);
        $merchant = $stmtMch->fetch(PDO::FETCH_ASSOC);
        $availableBalance = (float)($merchant['available_balance'] ?? 0.00);

        // 2. Metrics filtered by active environment mode (livemode)
        $stmtVol = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE merchant_id = ? AND status = 'succeeded' AND livemode = ?");
        $stmtVol->execute([$merchantId, $livemode]);
        $totalVolumePesewas = (float)$stmtVol->fetchColumn();
        $totalVolume = round($totalVolumePesewas / 100, 2);

        // Fallback to transactions table if payments table is empty
        if ($totalVolume == 0) {
            $stmtVolTx = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE merchant_id = ? AND status = 'successful'");
            $stmtVolTx->execute([$merchantId]);
            $totalVolume = (float)$stmtVolTx->fetchColumn();
        }

        $stmtTxCount = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE merchant_id = ? AND status = 'succeeded' AND livemode = ?");
        $stmtTxCount->execute([$merchantId, $livemode]);
        $successfulTxCount = (int)$stmtTxCount->fetchColumn();
        if ($successfulTxCount == 0) {
            $stmtTxCountOld = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE merchant_id = ? AND status = 'successful'");
            $stmtTxCountOld->execute([$merchantId]);
            $successfulTxCount = (int)$stmtTxCountOld->fetchColumn();
        }

        $stmtCustCount = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE merchant_id = ?");
        $stmtCustCount->execute([$merchantId]);
        $totalCustomers = (int)$stmtCustCount->fetchColumn();

        // 3. Recent Payment Intents filtered by active environment
        $stmtRecent = $pdo->prepare("
            SELECT p.*, c.name as customer_name, c.email as customer_email 
            FROM payments p 
            LEFT JOIN customers c ON p.customer_id = c.id 
            WHERE p.merchant_id = ? AND p.livemode = ? 
            ORDER BY p.created_at DESC LIMIT 5
        ");
        $stmtRecent->execute([$merchantId, $livemode]);
        $recentPayments = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

        // Fallback to transactions table if recent payments is empty
        if (empty($recentPayments)) {
            $stmtRecentOld = $pdo->prepare("
                SELECT t.*, t.reference as public_id, (t.amount * 100) as amount, c.name as customer_name, c.email as customer_email 
                FROM transactions t 
                LEFT JOIN customers c ON t.customer_id = c.id 
                WHERE t.merchant_id = ? 
                ORDER BY t.created_at DESC LIMIT 5
            ");
            $stmtRecentOld->execute([$merchantId]);
            $recentPayments = $stmtRecentOld->fetchAll(PDO::FETCH_ASSOC);
        }

        $chartLabels = ['May 1', 'May 5', 'May 10', 'May 15', 'May 20', 'May 25', 'May 31'];
        $chartData = ($env === 'test') ? [150, 450, 980, 1250, 2100, 3400, 4800] : [5000, 12800, 24800, 15200, 39800, 32100, 22100];

        View::render('dashboard/index', [
            'pageTitle' => 'Dashboard (' . strtoupper($env) . ' MODE)',
            'pageSubtitle' => "Welcome back, " . htmlspecialchars($user['name'] ?? 'John') . "! Viewing " . strtoupper($env) . " mode business metrics.",
            'merchant' => $merchant,
            'availableBalance' => $availableBalance,
            'totalVolume' => $totalVolume,
            'successfulTxCount' => $successfulTxCount,
            'totalCustomers' => $totalCustomers,
            'chartLabels' => json_encode($chartLabels),
            'chartData' => json_encode($chartData),
            'recentTransactions' => $recentPayments
        ]);
    }
}
