<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/Format.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/CsrfMiddleware.php';
require_once __DIR__ . '/../Services/SandboxPaymentGateway.php';
require_once __DIR__ . '/../Services/CsvExporter.php';
require_once __DIR__ . '/../../config/database.php';

class TransactionController {
    public function index(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();

        $user = Auth::user();
        $env = $user['environment'] ?? 'test';
        $livemode = ($env === 'live') ? 1 : 0;

        $search = trim($_GET['search'] ?? '');
        $statusTab = trim($_GET['status'] ?? 'all');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Query Stripe-style payments table filtered by livemode
        $where = ["p.merchant_id = ?", "p.livemode = ?"];
        $params = [$merchantId, $livemode];

        if (!empty($search)) {
            $where[] = "(p.public_id LIKE ? OR c.name LIKE ? OR c.email LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($statusTab !== 'all') {
            $where[] = "p.status = ?";
            $params[] = $statusTab;
        }

        $whereClause = implode(' AND ', $where);

        // Count total
        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM payments p LEFT JOIN customers c ON p.customer_id = c.id WHERE {$whereClause}");
        $stmtCount->execute($params);
        $totalRecords = (int)$stmtCount->fetchColumn();

        // Fallback to transactions table if payments count is 0
        if ($totalRecords === 0) {
            $whereOld = ["t.merchant_id = ?"];
            $paramsOld = [$merchantId];
            if (!empty($search)) {
                $whereOld[] = "(t.reference LIKE ? OR c.name LIKE ? OR c.email LIKE ?)";
                $paramsOld[] = "%{$search}%";
                $paramsOld[] = "%{$search}%";
                $paramsOld[] = "%{$search}%";
            }
            if ($statusTab !== 'all') {
                $whereOld[] = "t.status = ?";
                $paramsOld[] = $statusTab;
            }
            $whereOldClause = implode(' AND ', $whereOld);
            $stmtCountOld = $pdo->prepare("SELECT COUNT(*) FROM transactions t LEFT JOIN customers c ON t.customer_id = c.id WHERE {$whereOldClause}");
            $stmtCountOld->execute($paramsOld);
            $totalRecords = (int)$stmtCountOld->fetchColumn();

            $sqlOld = "SELECT t.id, t.reference as public_id, t.amount, t.fee, t.net_amount, t.currency, t.payment_method, t.status, t.created_at, c.name as customer_name, c.email as customer_email FROM transactions t LEFT JOIN customers c ON t.customer_id = c.id WHERE {$whereOldClause} ORDER BY t.created_at DESC LIMIT {$limit} OFFSET {$offset}";
            $stmtOld = $pdo->prepare($sqlOld);
            $stmtOld->execute($paramsOld);
            $transactions = $stmtOld->fetchAll();
        } else {
            $sql = "SELECT p.id, p.public_id, (p.amount / 100) as amount, round((p.amount / 100) * 0.015 + 0.50, 2) as fee, round((p.amount / 100) - ((p.amount / 100) * 0.015 + 0.50), 2) as net_amount, p.currency, p.payment_method, p.status, p.created_at, c.name as customer_name, c.email as customer_email FROM payments p LEFT JOIN customers c ON p.customer_id = c.id WHERE {$whereClause} ORDER BY p.created_at DESC LIMIT {$limit} OFFSET {$offset}";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $transactions = $stmt->fetchAll();
        }

        $totalPages = max(1, ceil($totalRecords / $limit));

        View::render('transactions/index', [
            'pageTitle' => 'Transactions (' . strtoupper($env) . ' MODE)',
            'pageSubtitle' => 'View and manage all your ' . strtoupper($env) . ' mode payment transactions.',
            'transactions' => $transactions,
            'statusTab' => $statusTab,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'limit' => $limit
        ]);
    }

    public function show(string $id): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();

        $stmt = $pdo->prepare("SELECT t.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone FROM transactions t LEFT JOIN customers c ON t.customer_id = c.id WHERE (t.id = ? OR t.reference = ?) AND t.merchant_id = ?");
        $stmt->execute([$id, $id, $merchantId]);
        $tx = $stmt->fetch();

        if (!$tx) {
            Response::setFlash('error', 'Transaction not found');
            Response::redirect('/transactions');
        }

        View::render('transactions/show', [
            'pageTitle' => 'Transaction Details',
            'pageSubtitle' => 'Reference: ' . $tx['reference'],
            'tx' => $tx
        ]);
    }

    public function refund(string $id): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $gateway = new SandboxPaymentGateway();
        $reason = trim($_POST['reason'] ?? 'Customer request');
        $amount = (float)($_POST['amount'] ?? 0);

        $res = $gateway->refund($id, $amount, $reason);

        if ($res['success']) {
            Response::setFlash('success', 'Refund processed successfully! Reference: ' . $res['refund_reference']);
        } else {
            Response::setFlash('error', $res['message']);
        }

        Response::redirect('/transactions/' . $id);
    }

    public function export(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();

        $stmt = $pdo->prepare("SELECT t.reference, c.name as customer, t.amount, t.fee, t.net_amount, t.currency, t.payment_method, t.status, t.created_at FROM transactions t LEFT JOIN customers c ON t.customer_id = c.id WHERE t.merchant_id = ? ORDER BY t.created_at DESC");
        $stmt->execute([$merchantId]);
        $data = $stmt->fetchAll();

        $headers = ['Transaction ID', 'Customer', 'Gross Amount', 'Fee', 'Net Amount', 'Currency', 'Payment Method', 'Status', 'Date'];
        
        $rows = array_map(function($row) {
            return [
                $row['reference'],
                $row['customer'] ?? 'Guest',
                $row['amount'],
                $row['fee'],
                $row['net_amount'],
                $row['currency'],
                $row['payment_method'],
                $row['status'],
                $row['created_at']
            ];
        }, $data);

        CsvExporter::download('transactions_export_' . date('Y-m-d') . '.csv', $headers, $rows);
    }
}
