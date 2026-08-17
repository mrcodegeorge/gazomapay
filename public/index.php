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
require_once __DIR__ . '/../app/Controllers/PaystackController.php';
require_once __DIR__ . '/../app/Controllers/DisputeController.php';
require_once __DIR__ . '/../app/Controllers/OnboardingController.php';

Auth::initSession();

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Auto-login default merchant user if dashboard route accessed without active session
$publicWebRoutes = ['/', '/solutions', '/pricing', '/developers', '/about', '/security', '/contact', '/login', '/register', '/checkout'];
if (!Auth::check() && !in_array($uri, $publicWebRoutes)) {
    if (strpos($uri, '/pay/') !== 0 && strpos($uri, '/api/') !== 0 && strpos($uri, '/checkout') !== 0) {
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

// Auth & Onboarding Routes
} elseif ($uri === '/login') {
    $ctrl = new AuthController();
    ($method === 'POST') ? $ctrl->processLogin() : $ctrl->showLogin();
} elseif ($uri === '/register') {
    $ctrl = new AuthController();
    ($method === 'POST') ? $ctrl->processRegister() : $ctrl->showRegister();
} elseif ($uri === '/logout') {
    (new AuthController())->logout();
} elseif ($uri === '/onboarding') {
    (new OnboardingController())->index();
} elseif ($uri === '/onboarding/step' && $method === 'POST') {
    (new OnboardingController())->saveStep();
} elseif ($uri === '/onboarding/complete' && $method === 'POST') {
    (new OnboardingController())->complete();

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

// Disputes
} elseif ($uri === '/disputes') {
    (new DisputeController())->index();
} elseif (preg_match('#^/disputes/([0-9]+)$#', $uri, $m)) {
    (new DisputeController())->show($m[1]);
} elseif (preg_match('#^/disputes/([0-9]+)/evidence$#', $uri, $m) && $method === 'POST') {
    (new DisputeController())->submitEvidence($m[1]);
} elseif (preg_match('#^/disputes/([0-9]+)/accept$#', $uri, $m) && $method === 'POST') {
    (new DisputeController())->acceptDispute($m[1]);

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

// Admin Routes
} elseif ($uri === '/admin') {
    (new AdminController())->index();
} elseif ($uri === '/admin/system-health') {
    (new AdminController())->systemHealth();
} elseif ($uri === '/admin/reconciliation') {
    (new AdminController())->reconciliation();

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
} elseif ($uri === '/subscriptions/create' && $method === 'POST') {
    (new SubscriptionController())->createSubscription();
} elseif (preg_match('#^/subscriptions/pause/([0-9]+)$#', $uri, $m) && $method === 'POST') {
    (new SubscriptionController())->togglePause($m[1]);
} elseif (preg_match('#^/subscriptions/cancel/([0-9]+)$#', $uri, $m) && $method === 'POST') {
    (new SubscriptionController())->cancelSubscription($m[1]);
} elseif (preg_match('#^/subscriptions/plan/delete/([0-9]+)$#', $uri, $m) && $method === 'POST') {
    (new SubscriptionController())->deletePlan($m[1]);

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
} elseif (($uri === '/settings/update-profile' || $uri === '/settings/profile') && $method === 'POST') {
    (new SettingsController())->updateProfile();
} elseif ($uri === '/settings/team/add' && $method === 'POST') {
    (new SettingsController())->addTeamMember();
} elseif ($uri === '/settings/toggle-mode' && $method === 'POST') {
    (new SettingsController())->toggleEnvironment();
} elseif (($uri === '/settings/update-password' || $uri === '/settings/password') && $method === 'POST') {
    (new SettingsController())->updatePassword();
} elseif ($uri === '/settings/rotate-keys' && $method === 'POST') {
    (new SettingsController())->rotateApiKeys();

// Super Admin Platform Dashboard & Standalone Pages
} elseif ($uri === '/admin' || $uri === '/admin/overview') {
    (new AdminController())->index();
} elseif (strpos($uri, '/admin/merchants') === 0 && $method === 'GET') {
    (new AdminController())->merchantsPage();
} elseif (strpos($uri, '/admin/settlements') === 0 && $method === 'GET') {
    (new AdminController())->settlementsPage();
} elseif (strpos($uri, '/admin/disputes') === 0 && $method === 'GET') {
    (new AdminController())->disputesPage();
} elseif (strpos($uri, '/admin/settings') === 0 && $method === 'GET') {
    (new AdminController())->settingsPage();
} elseif (strpos($uri, '/admin/audit-logs') === 0 && $method === 'GET') {
    (new AdminController())->auditLogsPage();
} elseif (preg_match('#^/admin/merchants/([0-9]+)/approve-kyc$#', $uri, $m) && $method === 'POST') {
    (new AdminController())->approveKyc($m[1]);
} elseif (preg_match('#^/admin/merchants/([0-9]+)/reject-kyc$#', $uri, $m) && $method === 'POST') {
    (new AdminController())->rejectKyc($m[1]);
} elseif (preg_match('#^/admin/merchants/([0-9]+)/toggle-status$#', $uri, $m) && $method === 'POST') {
    (new AdminController())->toggleStatus($m[1]);
} elseif (preg_match('#^/admin/merchants/([0-9]+)/update-fee$#', $uri, $m) && $method === 'POST') {
    (new AdminController())->updateMerchantFee($m[1]);
} elseif (preg_match('#^/admin/merchants/([0-9]+)/adjust-balance$#', $uri, $m) && $method === 'POST') {
    (new AdminController())->adjustMerchantBalance($m[1]);
} elseif (preg_match('#^/admin/settlements/([0-9]+)/process$#', $uri, $m) && $method === 'POST') {
    (new AdminController())->processSettlement($m[1]);
} elseif ($uri === '/admin/settings/update' && $method === 'POST') {
    (new AdminController())->updatePlatformSettings();
} elseif (preg_match('#^/admin/disputes/([0-9]+)/resolve$#', $uri, $m) && $method === 'POST') {
    (new AdminController())->resolveDispute($m[1]);

// Public Customer Checkout & Paystack Routes
} elseif ($uri === '/checkout') {
    View::render('checkout/index', ['amount' => 150.00, 'pageTitle' => 'Mobile Money Checkout'], 'pay');
} elseif ($uri === '/api/paystack/charge-momo' && $method === 'POST') {
    (new PaystackController())->chargeMomo();
} elseif (preg_match('#^/api/paystack/verify/([0-9a-zA-Z_]+)$#', $uri, $m)) {
    (new PaystackController())->verifyTransaction($m[1]);
} elseif ($uri === '/api/paystack/webhook' && $method === 'POST') {
    (new PaystackController())->handleWebhook();

// REST API v1 Hardened Endpoints
} elseif ($uri === '/api/v1/health') {
    (new ApiController())->health();
} elseif ($uri === '/api/v1/momo/charge' && $method === 'POST') {
    (new PaystackController())->chargeMomo();
} elseif (preg_match('#^/api/v1/momo/verify/([0-9a-zA-Z_]+)$#', $uri, $m)) {
    (new PaystackController())->verifyTransaction($m[1]);
} elseif ($uri === '/api/v1/momo/simulate-approval' && $method === 'POST') {
    (new PaystackController())->simulateMoMoApproval();
} elseif ($uri === '/api/v1/card/charge' && $method === 'POST') {
    (new ApiController())->chargeCard();
} elseif ($uri === '/api/v1/card/verify-3ds' && $method === 'POST') {
    $gateway = new SandboxPaymentGateway();
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $res = $gateway->verify3DsOtp($input['reference'] ?? '', $input['otp'] ?? '');
    Response::json($res);
// Payment Intents API Endpoints
} elseif ($uri === '/api/v1/payment-intents' && $method === 'POST') {
    (new ApiController())->createPaymentIntent();
} elseif (preg_match('#^/api/v1/payment-intents/([0-9a-zA-Z_]+)/confirm$#', $uri, $m) && $method === 'POST') {
    (new ApiController())->confirmPayment($m[1]);
} elseif (preg_match('#^/api/v1/payment-intents/([0-9a-zA-Z_]+)/cancel$#', $uri, $m) && $method === 'POST') {
    (new ApiController())->cancelPaymentIntent($m[1]);
} elseif (preg_match('#^/api/v1/payment-intents/([0-9a-zA-Z_]+)$#', $uri, $m) && $method === 'GET') {
    (new ApiController())->getPaymentIntent($m[1]);
} elseif ($uri === '/api/v1/payments' && $method === 'POST') {
    (new ApiController())->createPayment();
} elseif (preg_match('#^/api/v1/payments/([0-9a-zA-Z_]+)/confirm$#', $uri, $m) && $method === 'POST') {
    (new ApiController())->confirmPayment($m[1]);
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
} elseif ($uri === '/api/v1/merchant/environment' && $method === 'POST') {
    (new ApiController())->toggleEnvironmentApi();
} elseif ($uri === '/api/v1/settlements' && $method === 'GET') {
    (new ApiController())->listSettlements();
} else {
    http_response_code(404);
    echo "<h1>404 Not Found</h1><p>Gazoma Pay route does not exist.</p>";
}
