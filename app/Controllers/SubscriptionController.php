<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/Format.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/CsrfMiddleware.php';
require_once __DIR__ . '/../../config/database.php';

class SubscriptionController {
    public function index(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();

        // Subscription Plans
        $stmtPlans = $pdo->prepare("SELECT * FROM subscription_plans WHERE merchant_id = ? ORDER BY created_at DESC");
        $stmtPlans->execute([$merchantId]);
        $plans = $stmtPlans->fetchAll();

        // Active Subscriptions
        $stmtSubs = $pdo->prepare("SELECT s.*, c.name as customer_name, c.email as customer_email, p.name as plan_name, p.amount, p.billing_interval FROM subscriptions s JOIN customers c ON s.customer_id = c.id JOIN subscription_plans p ON s.plan_id = p.id WHERE s.merchant_id = ? ORDER BY s.created_at DESC");
        $stmtSubs->execute([$merchantId]);
        $subscriptions = $stmtSubs->fetchAll();

        View::render('subscriptions/index', [
            'pageTitle' => 'Subscriptions',
            'pageSubtitle' => 'Manage recurring payment plans and subscriber lifecycle.',
            'plans' => $plans,
            'subscriptions' => $subscriptions
        ]);
    }

    public function createPlan(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $merchantId = Auth::merchantId();
        $name = trim($_POST['name'] ?? '');
        $amount = (float)($_POST['amount'] ?? 0);
        $interval = $_POST['billing_interval'] ?? 'monthly';

        if (empty($name) || $amount <= 0) {
            Response::setFlash('error', 'Valid plan name and amount required');
            Response::redirect('/subscriptions');
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO subscription_plans (merchant_id, name, amount, currency, billing_interval, status) VALUES (?, ?, ?, 'GHS', ?, 'active')");
        $stmt->execute([$merchantId, $name, $amount, $interval]);

        AuditLogger::log('subscription_plan.created', "Created plan {$name} for GH₵ {$amount}/{$interval}");

        Response::setFlash('success', 'Subscription plan created!');
        Response::redirect('/subscriptions');
    }
}
