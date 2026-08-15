<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/Format.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/CsrfMiddleware.php';
require_once __DIR__ . '/../Services/AuditLogger.php';
require_once __DIR__ . '/../../config/database.php';

class SubscriptionController {

    public function index(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();

        // Subscription Plans
        $stmtPlans = $pdo->prepare("SELECT * FROM subscription_plans WHERE merchant_id = ? ORDER BY created_at DESC");
        $stmtPlans->execute([$merchantId]);
        $plans = $stmtPlans->fetchAll(PDO::FETCH_ASSOC);

        // Registered Customers (For Subscribe Customer modal)
        $stmtCust = $pdo->prepare("SELECT id, name, email FROM customers WHERE merchant_id = ? ORDER BY name ASC");
        $stmtCust->execute([$merchantId]);
        $customers = $stmtCust->fetchAll(PDO::FETCH_ASSOC);

        // Active & Recurring Subscriptions
        $stmtSubs = $pdo->prepare("
            SELECT s.*, 
                   c.name as customer_name, c.email as customer_email, 
                   p.name as plan_name, p.amount, p.billing_interval 
            FROM subscriptions s 
            JOIN customers c ON s.customer_id = c.id 
            JOIN subscription_plans p ON s.plan_id = p.id 
            WHERE s.merchant_id = ? 
            ORDER BY s.created_at DESC
        ");
        $stmtSubs->execute([$merchantId]);
        $subscriptions = $stmtSubs->fetchAll(PDO::FETCH_ASSOC);

        View::render('subscriptions/index', [
            'pageTitle' => 'Subscriptions',
            'pageSubtitle' => 'Manage recurring payment plans, subscriber lifecycle, and automated billing cycles.',
            'plans' => $plans,
            'customers' => $customers,
            'subscriptions' => $subscriptions
        ]);
    }

    public function createPlan(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $merchantId = Auth::merchantId();
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $amount = (float)($_POST['amount'] ?? 0);
        $interval = $_POST['billing_interval'] ?? 'monthly';
        $trialDays = (int)($_POST['trial_days'] ?? 0);

        if (empty($name) || $amount <= 0) {
            Response::setFlash('error', 'Valid plan name and positive amount required');
            Response::redirect('/subscriptions');
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO subscription_plans (merchant_id, name, description, amount, currency, billing_interval, trial_days, status) VALUES (?, ?, ?, ?, 'GHS', ?, ?, 'active')");
        $stmt->execute([$merchantId, $name, $description, $amount, $interval, $trialDays]);

        AuditLogger::log('subscription_plan.created', "Created plan {$name} for GH₵ {$amount}/{$interval}");

        Response::setFlash('success', 'Subscription plan created successfully!');
        Response::redirect('/subscriptions');
    }

    public function createSubscription(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $merchantId = Auth::merchantId();
        $customerId = (int)($_POST['customer_id'] ?? 0);
        $planId = (int)($_POST['plan_id'] ?? 0);
        $nextBilling = $_POST['next_billing_date'] ?? date('Y-m-d', strtotime('+30 days'));

        if ($customerId <= 0 || $planId <= 0) {
            Response::setFlash('error', 'Please select a valid customer and subscription plan.');
            Response::redirect('/subscriptions');
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO subscriptions (merchant_id, customer_id, plan_id, status, next_billing_date) VALUES (?, ?, ?, 'active', ?)");
        $stmt->execute([$merchantId, $customerId, $planId, $nextBilling]);

        AuditLogger::log('subscription.created', "Subscribed customer #{$customerId} to plan #{$planId}");

        Response::setFlash('success', 'Customer subscribed successfully to plan!');
        Response::redirect('/subscriptions');
    }

    public function togglePause(string $id): void {
        AuthMiddleware::handle();
        $merchantId = Auth::merchantId();
        $pdo = Database::getConnection();

        $stmtFetch = $pdo->prepare("SELECT status FROM subscriptions WHERE id = ? AND merchant_id = ?");
        $stmtFetch->execute([(int)$id, $merchantId]);
        $currentStatus = $stmtFetch->fetchColumn();

        if ($currentStatus) {
            $newStatus = ($currentStatus === 'active') ? 'paused' : 'active';
            $stmtUpd = $pdo->prepare("UPDATE subscriptions SET status = ? WHERE id = ? AND merchant_id = ?");
            $stmtUpd->execute([$newStatus, (int)$id, $merchantId]);

            AuditLogger::log('subscription.status_updated', "Updated subscription #{$id} status to {$newStatus}");
            Response::setFlash('success', "Subscription status updated to {$newStatus}.");
        }

        Response::redirect('/subscriptions');
    }

    public function cancelSubscription(string $id): void {
        AuthMiddleware::handle();
        $merchantId = Auth::merchantId();
        $pdo = Database::getConnection();

        $stmtUpd = $pdo->prepare("UPDATE subscriptions SET status = 'cancelled' WHERE id = ? AND merchant_id = ?");
        $stmtUpd->execute([(int)$id, $merchantId]);

        AuditLogger::log('subscription.cancelled', "Cancelled subscription #{$id}");
        Response::setFlash('success', 'Subscription cancelled.');

        Response::redirect('/subscriptions');
    }

    public function deletePlan(string $id): void {
        AuthMiddleware::handle();
        $merchantId = Auth::merchantId();
        $pdo = Database::getConnection();

        $stmtDel = $pdo->prepare("DELETE FROM subscription_plans WHERE id = ? AND merchant_id = ?");
        $stmtDel->execute([(int)$id, $merchantId]);

        AuditLogger::log('subscription_plan.deleted', "Deleted subscription plan #{$id}");
        Response::setFlash('success', 'Subscription plan deleted.');

        Response::redirect('/subscriptions');
    }
}
