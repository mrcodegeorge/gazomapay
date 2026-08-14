<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/Format.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/CsrfMiddleware.php';
require_once __DIR__ . '/../../config/database.php';

class CustomerController {
    public function index(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();

        $search = trim($_GET['search'] ?? '');
        $where = ["merchant_id = ?"];
        $params = [$merchantId];

        if (!empty($search)) {
            $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $whereClause = implode(' AND ', $where);
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE {$whereClause} ORDER BY created_at DESC");
        $stmt->execute($params);
        $customers = $stmt->fetchAll();

        View::render('customers/index', [
            'pageTitle' => 'Customers',
            'pageSubtitle' => 'View and manage your customer accounts and transaction history.',
            'customers' => $customers,
            'search' => $search
        ]);
    }

    public function show(string $id): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();

        $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ? AND merchant_id = ?");
        $stmt->execute([$id, $merchantId]);
        $customer = $stmt->fetch();

        if (!$customer) {
            Response::setFlash('error', 'Customer not found');
            Response::redirect('/customers');
        }

        // Fetch Recent Transactions for Customer
        $stmtTx = $pdo->prepare("SELECT * FROM transactions WHERE customer_id = ? ORDER BY created_at DESC");
        $stmtTx->execute([$customer['id']]);
        $txs = $stmtTx->fetchAll();

        View::render('customers/show', [
            'pageTitle' => $customer['name'],
            'pageSubtitle' => 'Customer profile & spending breakdown',
            'customer' => $customer,
            'transactions' => $txs
        ]);
    }

    public function store(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $merchantId = Auth::merchantId();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($name) || empty($email)) {
            Response::setFlash('error', 'Customer name and email are required');
            Response::redirect('/customers');
        }

        $uuid = 'cst_' . bin2hex(random_bytes(6));
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO customers (merchant_id, uuid, name, email, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$merchantId, $uuid, $name, $email, $phone]);

        AuditLogger::log('customer.created', "Added customer {$name} ({$email})");

        Response::setFlash('success', 'Customer added successfully!');
        Response::redirect('/customers');
    }
}
