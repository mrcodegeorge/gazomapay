<?php

require_once __DIR__ . '/../Middleware/ApiAuthMiddleware.php';
require_once __DIR__ . '/../Services/SandboxPaymentGateway.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../../config/database.php';

class ApiController {
    public function createPayment(): void {
        $apiKey = ApiAuthMiddleware::authenticate();
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $gateway = new SandboxPaymentGateway();
        $res = $gateway->charge([
            'merchant_id' => $apiKey['merchant_id'],
            'amount' => (float)($input['amount'] ?? 0),
            'customer_name' => $input['customer_name'] ?? 'API Customer',
            'customer_email' => $input['customer_email'] ?? 'api@example.com',
            'customer_phone' => $input['customer_phone'] ?? '',
            'payment_method' => $input['payment_method'] ?? 'card'
        ]);

        Response::json($res, $res['success'] ? 200 : 400);
    }

    public function getPayment(string $id): void {
        $apiKey = ApiAuthMiddleware::authenticate();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE (id = ? OR reference = ?) AND merchant_id = ?");
        $stmt->execute([$id, $id, $apiKey['merchant_id']]);
        $tx = $stmt->fetch();

        if (!$tx) {
            Response::json(['error' => 'Payment transaction not found'], 404);
        }

        Response::json(['success' => true, 'data' => $tx]);
    }

    public function createCustomer(): void {
        $apiKey = ApiAuthMiddleware::authenticate();
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? '');

        if (empty($name) || empty($email)) {
            Response::json(['error' => 'Name and email are required'], 400);
        }

        $pdo = Database::getConnection();
        $uuid = 'cst_' . bin2hex(random_bytes(6));
        $stmt = $pdo->prepare("INSERT INTO customers (merchant_id, uuid, name, email, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$apiKey['merchant_id'], $uuid, $name, $email, $phone]);

        Response::json(['success' => true, 'id' => $pdo->lastInsertId(), 'uuid' => $uuid, 'name' => $name, 'email' => $email]);
    }

    public function getCustomer(string $id): void {
        $apiKey = ApiAuthMiddleware::authenticate();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM customers WHERE (id = ? OR uuid = ?) AND merchant_id = ?");
        $stmt->execute([$id, $id, $apiKey['merchant_id']]);
        $cust = $stmt->fetch();

        if (!$cust) {
            Response::json(['error' => 'Customer not found'], 404);
        }

        Response::json(['success' => true, 'data' => $cust]);
    }
}
