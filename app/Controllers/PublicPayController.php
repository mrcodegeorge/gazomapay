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
        $stmt = $pdo->prepare("SELECT pl.*, m.name as merchant_name, m.email as merchant_email, m.logo as merchant_logo, m.environment as merchant_environment FROM payment_links pl JOIN merchants m ON pl.merchant_id = m.id WHERE pl.token = ? AND pl.status = 'active'");
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
        $provider = trim($_POST['provider'] ?? 'mtn');

        if (empty($customerName) || empty($customerEmail)) {
            Response::json(['success' => false, 'message' => 'Name and email are required'], 400);
        }

        // If Mobile Money selected, process via white-labeled Paystack Charge API
        if ($paymentMethod === 'mobile_money') {
            require_once __DIR__ . '/PaystackController.php';
            $_POST['amount'] = $link['amount'];
            $_POST['email'] = $customerEmail;
            $_POST['phone'] = $customerPhone;
            $_POST['provider'] = $provider;
            (new PaystackController())->chargeMomo();
            return;
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

        if (!empty($result['success']) && !empty($link['subscription_plan_id'])) {
            $stmtCust = $pdo->prepare("SELECT id FROM customers WHERE merchant_id = ? AND email = ?");
            $stmtCust->execute([$link['merchant_id'], $customerEmail]);
            $cId = $stmtCust->fetchColumn();

            if ($cId) {
                $stmtPlan = $pdo->prepare("SELECT billing_interval FROM subscription_plans WHERE id = ?");
                $stmtPlan->execute([$link['subscription_plan_id']]);
                $interval = $stmtPlan->fetchColumn() ?: 'monthly';

                $daysMap = ['daily' => 1, 'weekly' => 7, 'monthly' => 30, 'quarterly' => 90, 'yearly' => 365];
                $addDays = $daysMap[$interval] ?? 30;
                $nextBillingDate = date('Y-m-d', strtotime("+{$addDays} days"));

                $insSub = $pdo->prepare("INSERT INTO subscriptions (merchant_id, customer_id, plan_id, status, next_billing_date) VALUES (?, ?, ?, 'active', ?)");
                $insSub->execute([$link['merchant_id'], $cId, $link['subscription_plan_id'], $nextBillingDate]);
            }
        }

        Response::json($result);
    }
}
