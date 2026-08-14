<?php

require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/Format.php';
require_once __DIR__ . '/../Services/SandboxPaymentGateway.php';
require_once __DIR__ . '/../../config/database.php';

class PublicPayController {
    public function show(string $token): void {
        $pdo = Database::getConnection();

        // 1. Fetch Payment Link
        $stmt = $pdo->prepare("SELECT pl.*, m.name as merchant_name, m.email as merchant_email, m.logo as merchant_logo FROM payment_links pl JOIN merchants m ON pl.merchant_id = m.id WHERE pl.token = ? AND pl.status = 'active'");
        $stmt->execute([$token]);
        $link = $stmt->fetch();

        if (!$link) {
            die("Payment link invalid or expired.");
        }

        // 2. Record View Count
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Browser';
        $insView = $pdo->prepare("INSERT INTO payment_link_views (payment_link_id, ip_address, user_agent) VALUES (?, ?, ?)");
        $insView->execute([$link['id'], $ip, $ua]);

        View::render('pay/checkout', [
            'pageTitle' => 'Pay ' . htmlspecialchars($link['merchant_name']),
            'link' => $link
        ], 'pay');
    }

    public function process(string $token): void {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM payment_links WHERE token = ? AND status = 'active'");
        $stmt->execute([$token]);
        $link = $stmt->fetch();

        if (!$link) {
            Response::json(['success' => false, 'message' => 'Payment link invalid'], 400);
        }

        $customerName = trim($_POST['customer_name'] ?? '');
        $customerEmail = trim($_POST['customer_email'] ?? '');
        $customerPhone = trim($_POST['customer_phone'] ?? '');
        $paymentMethod = trim($_POST['payment_method'] ?? 'card');

        if (empty($customerName) || empty($customerEmail)) {
            Response::json(['success' => false, 'message' => 'Name and email are required'], 400);
        }

        $gateway = new SandboxPaymentGateway();
        $result = $gateway->charge([
            'merchant_id' => $link['merchant_id'],
            'payment_link_id' => $link['id'],
            'amount' => $link['amount'],
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
            'payment_method' => $paymentMethod
        ]);

        Response::json($result);
    }
}
