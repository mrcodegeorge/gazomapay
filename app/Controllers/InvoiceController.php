<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/Format.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/CsrfMiddleware.php';
require_once __DIR__ . '/../Services/PdfGenerator.php';
require_once __DIR__ . '/../../config/database.php';

class InvoiceController {
    public function index(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();

        $stmt = $pdo->prepare("SELECT i.*, c.name as customer_name, c.email as customer_email FROM invoices i JOIN customers c ON i.customer_id = c.id WHERE i.merchant_id = ? ORDER BY i.created_at DESC");
        $stmt->execute([$merchantId]);
        $invoices = $stmt->fetchAll();

        // Customers for create modal
        $stmtCust = $pdo->prepare("SELECT id, name, email FROM customers WHERE merchant_id = ? ORDER BY name");
        $stmtCust->execute([$merchantId]);
        $customers = $stmtCust->fetchAll();

        View::render('invoices/index', [
            'pageTitle' => 'Invoices',
            'pageSubtitle' => 'Create and manage professional invoices for your clients.',
            'invoices' => $invoices,
            'customers' => $customers
        ]);
    }

    public function store(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $merchantId = Auth::merchantId();
        $customerId = (int)($_POST['customer_id'] ?? 0);
        $dueDate = $_POST['due_date'] ?? date('Y-m-d', strtotime('+14 days'));
        $description = trim($_POST['description'] ?? 'Professional Consulting Services');
        $amount = (float)($_POST['amount'] ?? 0);

        if (!$customerId || $amount <= 0) {
            Response::setFlash('error', 'Please select a customer and enter a valid amount.');
            Response::redirect('/invoices');
        }

        $pdo = Database::getConnection();
        $invNum = 'INV-2024-' . sprintf('%03d', rand(10, 999));

        $stmtInv = $pdo->prepare("INSERT INTO invoices (merchant_id, customer_id, invoice_number, subtotal, tax, total, status, due_date) VALUES (?, ?, ?, ?, 0.00, ?, 'sent', ?)");
        $stmtInv->execute([$merchantId, $customerId, $invNum, $amount, $amount, $dueDate]);
        $invoiceId = $pdo->lastInsertId();

        $stmtItem = $pdo->prepare("INSERT INTO invoice_items (invoice_id, description, quantity, unit_price, amount) VALUES (?, ?, 1, ?, ?)");
        $stmtItem->execute([$invoiceId, $description, $amount, $amount]);

        AuditLogger::log('invoice.created', "Created invoice {$invNum} for GH₵ {$amount}");

        Response::setFlash('success', 'Invoice ' . $invNum . ' created and sent!');
        Response::redirect('/invoices');
    }

    public function pdf(string $id): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();

        $stmt = $pdo->prepare("SELECT i.*, c.name as customer_name, c.email as customer_email FROM invoices i JOIN customers c ON i.customer_id = c.id WHERE i.id = ? AND i.merchant_id = ?");
        $stmt->execute([$id, $merchantId]);
        $inv = $stmt->fetch();

        if (!$inv) die("Invoice not found.");

        $stmtItems = $pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
        $stmtItems->execute([$inv['id']]);
        $items = $stmtItems->fetchAll();

        $user = Auth::user();
        $merchant = [
            'name' => $user['merchant_name'],
            'email' => $user['email'],
            'logo' => $user['merchant_logo'],
            'address' => '15 Independence Avenue, Accra, Ghana'
        ];

        echo PdfGenerator::renderInvoiceHtml($inv, $items, $merchant);
        exit;
    }
}
