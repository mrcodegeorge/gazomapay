<?php

require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Middleware/ApiAuthMiddleware.php';
require_once __DIR__ . '/../Services/SandboxPaymentGateway.php';
require_once __DIR__ . '/../Services/IdempotencyService.php';
require_once __DIR__ . '/../Services/LedgerEngine.php';
require_once __DIR__ . '/../../config/database.php';

class ApiController {

    private static function respondSuccess(array $data, int $code = 200): void {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'data' => $data,
            'meta' => [
                'timestamp' => date('c'),
                'version' => 'v1'
            ]
        ], JSON_PRETTY_PRINT);
        exit;
    }

    private static function respondError(string $errorCode, string $message, int $code = 400): void {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => $errorCode,
                'message' => $message
            ]
        ], JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * GET /api/v1/health
     */
    public function health(): void {
        $dbHealthy = false;
        try {
            $pdo = Database::getConnection();
            $pdo->query("SELECT 1");
            $dbHealthy = true;
        } catch (Exception $e) {}

        $mode = Env::get('GAZOMA_PAYMENT_MODE', 'sandbox');

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => $dbHealthy ? 'healthy' : 'degraded',
            'timestamp' => date('c'),
            'subsystems' => [
                'database' => $dbHealthy ? 'healthy' : 'unhealthy',
                'ledger' => 'healthy',
                'payment_providers' => [
                    'mode' => $mode,
                    'sandbox' => 'healthy',
                    'paystack' => Env::get('PAYSTACK_ENABLED', false) ? 'healthy' : 'not_configured',
                    'hubtel' => Env::get('HUBTEL_ENABLED', false) ? 'healthy' : 'not_configured'
                ]
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * POST /api/v1/payments
     */
    public function createPayment(): void {
        $merchant = ApiAuthMiddleware::authenticate();
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $idempotencyKey = $_SERVER['HTTP_IDEMPOTENCY_KEY'] ?? null;
        if ($idempotencyKey) {
            $cached = IdempotencyService::check($merchant['id'], $idempotencyKey, '/api/v1/payments', $input);
            if ($cached) {
                header('Content-Type: application/json');
                http_response_code($cached['code']);
                echo json_encode($cached['body'], JSON_PRETTY_PRINT);
                exit;
            }
        }

        $amount = (float)($input['amount'] ?? 0);
        if ($amount <= 0) {
            self::respondError('INVALID_AMOUNT', 'The payment amount must be greater than zero.', 422);
        }

        $gateway = new SandboxPaymentGateway();
        $input['merchant_id'] = $merchant['id'];
        $result = $gateway->charge($input);

        if ($result['success']) {
            $responsePayload = [
                'success' => true,
                'data' => $result,
                'meta' => ['timestamp' => date('c'), 'version' => 'v1']
            ];
            if ($idempotencyKey) {
                IdempotencyService::store($merchant['id'], $idempotencyKey, '/api/v1/payments', $input, 200, $responsePayload);
            }
            self::respondSuccess($result, 200);
        } else {
            self::respondError('PAYMENT_FAILED', $result['message'], 400);
        }
    }

    /**
     * GET /api/v1/payments/{id}
     */
    public function getPayment(string $id): void {
        $merchant = ApiAuthMiddleware::authenticate();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE merchant_id = ? AND (reference = ? OR id = ?)");
        $stmt->execute([$merchant['id'], $id, $id]);
        $tx = $stmt->fetch();

        if (!$tx) {
            self::respondError('NOT_FOUND', 'Payment transaction not found.', 404);
        }

        self::respondSuccess($tx);
    }

    /**
     * POST /api/v1/payments/{id}/refund
     */
    public function refundPayment(string $id): void {
        $merchant = ApiAuthMiddleware::authenticate();
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $gateway = new SandboxPaymentGateway();
        $result = $gateway->refund($id, (float)($input['amount'] ?? 0), $input['reason'] ?? 'API initiated refund');

        if ($result['success']) {
            self::respondSuccess($result);
        } else {
            self::respondError('REFUND_FAILED', $result['message'], 400);
        }
    }

    /**
     * GET /api/v1/transactions
     */
    public function listTransactions(): void {
        $merchant = ApiAuthMiddleware::authenticate();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE merchant_id = ? ORDER BY id DESC LIMIT 50");
        $stmt->execute([$merchant['id']]);
        $txs = $stmt->fetchAll();

        self::respondSuccess(['transactions' => $txs, 'count' => count($txs)]);
    }

    /**
     * POST /api/v1/customers & GET /api/v1/customers
     */
    public function createCustomer(): void {
        $merchant = ApiAuthMiddleware::authenticate();
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? '');

        if (!$name || !$email) {
            self::respondError('INVALID_INPUT', 'Customer name and email are required.', 422);
        }

        $pdo = Database::getConnection();
        $uuid = 'cst_' . bin2hex(random_bytes(6));
        $stmt = $pdo->prepare("INSERT INTO customers (merchant_id, uuid, name, email, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$merchant['id'], $uuid, $name, $email, $phone]);

        $id = $pdo->lastInsertId();
        self::respondSuccess(['id' => $id, 'uuid' => $uuid, 'name' => $name, 'email' => $email], 201);
    }

    public function listCustomers(): void {
        $merchant = ApiAuthMiddleware::authenticate();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM customers WHERE merchant_id = ? ORDER BY id DESC LIMIT 50");
        $stmt->execute([$merchant['id']]);
        $customers = $stmt->fetchAll();

        self::respondSuccess(['customers' => $customers, 'count' => count($customers)]);
    }

    public function getCustomer(string $id): void {
        $merchant = ApiAuthMiddleware::authenticate();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM customers WHERE merchant_id = ? AND (uuid = ? OR id = ?)");
        $stmt->execute([$merchant['id'], $id, $id]);
        $cst = $stmt->fetch();

        if (!$cst) {
            self::respondError('NOT_FOUND', 'Customer not found.', 404);
        }

        self::respondSuccess($cst);
    }

    /**
     * POST /api/v1/payment-links & GET /api/v1/payment-links
     */
    public function createPaymentLink(): void {
        $merchant = ApiAuthMiddleware::authenticate();
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $name = trim($input['name'] ?? '');
        $amount = (float)($input['amount'] ?? 0);

        if (!$name || $amount <= 0) {
            self::respondError('INVALID_INPUT', 'Valid link name and amount are required.', 422);
        }

        $pdo = Database::getConnection();
        $token = 'PL_' . sprintf('%08d', rand(100000, 99999999));
        $stmt = $pdo->prepare("INSERT INTO payment_links (merchant_id, token, name, description, amount, currency, max_uses, redirect_url) VALUES (?, ?, ?, ?, ?, 'GHS', ?, ?)");
        $stmt->execute([
            $merchant['id'],
            $token,
            $name,
            $input['description'] ?? '',
            $amount,
            (int)($input['max_uses'] ?? 0),
            $input['redirect_url'] ?? ''
        ]);

        $id = $pdo->lastInsertId();
        self::respondSuccess([
            'id' => $id,
            'token' => $token,
            'name' => $name,
            'amount' => $amount,
            'url' => "http://127.0.0.1:8000/pay/{$token}"
        ], 201);
    }

    public function listPaymentLinks(): void {
        $merchant = ApiAuthMiddleware::authenticate();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM payment_links WHERE merchant_id = ? ORDER BY id DESC");
        $stmt->execute([$merchant['id']]);
        $links = $stmt->fetchAll();

        self::respondSuccess(['payment_links' => $links, 'count' => count($links)]);
    }

    public function getPaymentLink(string $id): void {
        $merchant = ApiAuthMiddleware::authenticate();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM payment_links WHERE merchant_id = ? AND (token = ? OR id = ?)");
        $stmt->execute([$merchant['id'], $id, $id]);
        $link = $stmt->fetch();

        if (!$link) {
            self::respondError('NOT_FOUND', 'Payment link not found.', 404);
        }

        self::respondSuccess($link);
    }

    /**
     * GET /api/v1/balance
     */
    public function getBalance(): void {
        $merchant = ApiAuthMiddleware::authenticate();

        $avail = LedgerEngine::getAvailableBalance($merchant['id']);
        $pending = LedgerEngine::getPendingBalance($merchant['id']);
        $settled = LedgerEngine::getSettledBalance($merchant['id']);

        self::respondSuccess([
            'currency' => 'GHS',
            'available_balance' => $avail,
            'pending_balance' => $pending,
            'settled_balance' => $settled,
            'total_balance' => round($avail + $pending, 2)
        ]);
    }

    /**
     * GET /api/v1/settlements
     */
    public function listSettlements(): void {
        $merchant = ApiAuthMiddleware::authenticate();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM settlements WHERE merchant_id = ? ORDER BY id DESC");
        $stmt->execute([$merchant['id']]);
        $settlements = $stmt->fetchAll();

        self::respondSuccess(['settlements' => $settlements, 'count' => count($settlements)]);
    }

    /**
     * POST /api/v1/card/charge
     */
    public function chargeCard(): void {
        $merchant = ApiAuthMiddleware::authenticate();
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $input['merchant_id'] = $merchant['id'];
        $input['payment_method'] = 'card';
        
        $gateway = new SandboxPaymentGateway();
        $result = $gateway->charge($input);

        if ($result['success']) {
            self::respondSuccess($result);
        } else {
            self::respondError('CARD_CHARGE_FAILED', $result['message'], 400);
        }
    }

    /**
     * POST /api/v1/card/verify-3ds
     */
    public function verify3Ds(): void {
        $merchant = ApiAuthMiddleware::authenticate();
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $reference = trim($input['reference'] ?? '');
        $otp = trim($input['otp'] ?? '');

        if (!$reference || !$otp) {
            self::respondError('INVALID_INPUT', 'Transaction reference and OTP are required.', 422);
        }

        $gateway = new SandboxPaymentGateway();
        $result = $gateway->verify3DsOtp($reference, $otp);

        if ($result['success']) {
            self::respondSuccess($result);
        } else {
            self::respondError('3DS_VERIFICATION_FAILED', $result['message'], 400);
        }
    }
}
