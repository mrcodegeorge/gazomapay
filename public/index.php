<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Helpers/Auth.php';
require_once __DIR__ . '/../app/Helpers/View.php';
require_once __DIR__ . '/../app/Helpers/Response.php';
require_once __DIR__ . '/../app/Helpers/Format.php';

// Controllers
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';
require_once __DIR__ . '/../app/Controllers/TransactionController.php';
require_once __DIR__ . '/../app/Controllers/CustomerController.php';
require_once __DIR__ . '/../app/Controllers/SettlementController.php';
require_once __DIR__ . '/../app/Controllers/PaymentLinkController.php';
require_once __DIR__ . '/../app/Controllers/PublicPayController.php';
require_once __DIR__ . '/../app/Controllers/InvoiceController.php';
require_once __DIR__ . '/../app/Controllers/SubscriptionController.php';
require_once __DIR__ . '/../app/Controllers/AnalyticsController.php';
require_once __DIR__ . '/../app/Controllers/DeveloperController.php';
require_once __DIR__ . '/../app/Controllers/SettingsController.php';
require_once __DIR__ . '/../app/Controllers/AdminController.php';
require_once __DIR__ . '/../app/Controllers/ApiController.php';
require_once __DIR__ . '/../app/Controllers/PublicWebController.php';

Auth::initSession();

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Auto-login default merchant user if dashboard route accessed without active session
$publicWebRoutes = ['/', '/solutions', '/pricing', '/developers', '/about', '/security', '/contact', '/login', '/register'];
if (!Auth::check() && !in_array($uri, $publicWebRoutes)) {
    if (strpos($uri, '/pay/') !== 0 && strpos($uri, '/api/') !== 0) {
        Auth::login('admin@gazomapay.com', 'password123');
    }
}

// Public Web Marketing Site Routes
if ($uri === '/') {
    (new PublicWebController())->home();
} elseif ($uri === '/solutions') {
    (new PublicWebController())->solutions();
} elseif ($uri === '/pricing') {
    (new PublicWebController())->pricing();
} elseif ($uri === '/developers') {
    (new PublicWebController())->developers();
} elseif ($uri === '/about') {
    (new PublicWebController())->about();
} elseif ($uri === '/security') {
    (new PublicWebController())->security();
} elseif ($uri === '/contact') {
    (new PublicWebController())->contact();

// Auth Routes
} elseif ($uri === '/login') {
    $ctrl = new AuthController();
    ($method === 'POST') ? $ctrl->processLogin() : $ctrl->showLogin();
} elseif ($uri === '/register') {
    $ctrl = new AuthController();
    ($method === 'POST') ? $ctrl->processRegister() : $ctrl->showRegister();
} elseif ($uri === '/logout') {
    (new AuthController())->logout();

// Merchant Dashboard
} elseif ($uri === '/dashboard') {
    (new DashboardController())->index();

// Transactions
} elseif ($uri === '/transactions') {
    (new TransactionController())->index();
} elseif (preg_match('#^/transactions/([0-9a-zA-Z_]+)$#', $uri, $m)) {
    (new TransactionController())->show($m[1]);
} elseif (preg_match('#^/transactions/([0-9a-zA-Z_]+)/refund$#', $uri, $m)) {
    (new TransactionController())->refund($m[1]);
} elseif ($uri === '/transactions/export') {
    (new TransactionController())->export();

// Customers
} elseif ($uri === '/customers') {
    (new CustomerController())->index();
} elseif ($uri === '/customers/create' && $method === 'POST') {
    (new CustomerController())->store();
} elseif (preg_match('#^/customers/([0-9]+)$#', $uri, $m)) {
    (new CustomerController())->show($m[1]);

// Settlements
} elseif ($uri === '/settlements') {
    (new SettlementController())->index();
} elseif ($uri === '/settlements/request' && $method === 'POST') {
    (new SettlementController())->request();

// Payment Links
} elseif ($uri === '/payment-links') {
    (new PaymentLinkController())->index();
} elseif ($uri === '/payment-links/create' && $method === 'POST') {
    (new PaymentLinkController())->store();
} elseif (preg_match('#^/payment-links/([0-9]+)/analytics$#', $uri, $m)) {
    (new PaymentLinkController())->analytics($m[1]);

// Public Customer Checkout
} elseif (preg_match('#^/pay/([0-9a-zA-Z_]+)$#', $uri, $m)) {
    $ctrl = new PublicPayController();
    ($method === 'POST') ? $ctrl->process($m[1]) : $ctrl->show($m[1]);

// Invoices
} elseif ($uri === '/invoices') {
    (new InvoiceController())->index();
} elseif ($uri === '/invoices/create' && $method === 'POST') {
    (new InvoiceController())->store();
} elseif (preg_match('#^/invoices/([0-9]+)/pdf$#', $uri, $m)) {
    (new InvoiceController())->pdf($m[1]);

// Subscriptions
} elseif ($uri === '/subscriptions') {
    (new SubscriptionController())->index();
} elseif ($uri === '/subscriptions/plan/create' && $method === 'POST') {
    (new SubscriptionController())->createPlan();

// Analytics & Reports
} elseif ($uri === '/analytics') {
    (new AnalyticsController())->index();
} elseif ($uri === '/analytics/report-csv') {
    (new TransactionController())->export();

// Developer Portal
} elseif ($uri === '/developer') {
    (new DeveloperController())->index();
} elseif ($uri === '/developer/api-keys/create' && $method === 'POST') {
    (new DeveloperController())->generateApiKey();
} elseif ($uri === '/developer/webhooks/create' && $method === 'POST') {
    (new DeveloperController())->addWebhook();
} elseif (preg_match('#^/developer/webhook-log/([0-9]+)/retry$#', $uri, $m)) {
    (new DeveloperController())->retryWebhook($m[1]);

// Settings
} elseif ($uri === '/settings' || $uri === '/settings/profile') {
    (new SettingsController())->index();
} elseif ($uri === '/settings/profile' && $method === 'POST') {
    (new SettingsController())->updateProfile();
} elseif ($uri === '/settings/team/add' && $method === 'POST') {
    (new SettingsController())->addTeamMember();

// Admin Panel
} elseif ($uri === '/admin') {
    (new AdminController())->index();

// REST API v1 Hardened Endpoints
} elseif ($uri === '/api/v1/payments' && $method === 'POST') {
    (new ApiController())->createPayment();
} elseif (preg_match('#^/api/v1/payments/([0-9a-zA-Z_]+)/refund$#', $uri, $m) && $method === 'POST') {
    (new ApiController())->refundPayment($m[1]);
} elseif (preg_match('#^/api/v1/payments/([0-9a-zA-Z_]+)$#', $uri, $m)) {
    (new ApiController())->getPayment($m[1]);
} elseif ($uri === '/api/v1/transactions' && $method === 'GET') {
    (new ApiController())->listTransactions();
} elseif ($uri === '/api/v1/customers' && $method === 'POST') {
    (new ApiController())->createCustomer();
} elseif ($uri === '/api/v1/customers' && $method === 'GET') {
    (new ApiController())->listCustomers();
} elseif (preg_match('#^/api/v1/customers/([0-9a-zA-Z_]+)$#', $uri, $m)) {
    (new ApiController())->getCustomer($m[1]);
} elseif ($uri === '/api/v1/payment-links' && $method === 'POST') {
    (new ApiController())->createPaymentLink();
} elseif ($uri === '/api/v1/payment-links' && $method === 'GET') {
    (new ApiController())->listPaymentLinks();
} elseif (preg_match('#^/api/v1/payment-links/([0-9a-zA-Z_]+)$#', $uri, $m)) {
    (new ApiController())->getPaymentLink($m[1]);
} elseif ($uri === '/api/v1/balance' && $method === 'GET') {
    (new ApiController())->getBalance();
} elseif ($uri === '/api/v1/settlements' && $method === 'GET') {
    (new ApiController())->listSettlements();
} else {
    http_response_code(404);
    echo "<h1>404 Not Found</h1><p>Gazoma Pay route does not exist.</p>";
}
