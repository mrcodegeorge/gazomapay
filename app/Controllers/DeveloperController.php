<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/Format.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/CsrfMiddleware.php';
require_once __DIR__ . '/../Services/AuditLogger.php';
require_once __DIR__ . '/../Services/WebhookDispatcher.php';
require_once __DIR__ . '/../../config/database.php';

class DeveloperController {
    public function index(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();

        // API Keys
        $stmtKeys = $pdo->prepare("SELECT * FROM api_keys WHERE merchant_id = ? ORDER BY created_at DESC");
        $stmtKeys->execute([$merchantId]);
        $apiKeys = $stmtKeys->fetchAll();

        // Webhook Endpoints
        $stmtWh = $pdo->prepare("SELECT * FROM webhook_endpoints WHERE merchant_id = ? ORDER BY created_at DESC");
        $stmtWh->execute([$merchantId]);
        $webhooks = $stmtWh->fetchAll();

        // Webhook Delivery Logs
        $stmtLogs = $pdo->prepare("SELECT l.*, e.url FROM webhook_logs l JOIN webhook_endpoints e ON l.endpoint_id = e.id WHERE l.merchant_id = ? ORDER BY l.created_at DESC LIMIT 10");
        $stmtLogs->execute([$merchantId]);
        $webhookLogs = $stmtLogs->fetchAll();

        View::render('developer/index', [
            'pageTitle' => 'Developer Portal',
            'pageSubtitle' => 'Manage API keys, configure webhooks, and explore REST API documentation.',
            'apiKeys' => $apiKeys,
            'webhooks' => $webhooks,
            'webhookLogs' => $webhookLogs
        ]);
    }

    public function generateApiKey(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $merchantId = Auth::merchantId();
        $name = trim($_POST['name'] ?? 'New API Key');
        $keyType = $_POST['key_type'] ?? 'live';

        $pubKey = 'gzm_' . $keyType . '_pub_' . bin2hex(random_bytes(10));
        $secKey = 'gzm_' . $keyType . '_sec_' . bin2hex(random_bytes(16));
        $secHash = password_hash($secKey, PASSWORD_BCRYPT);
        $secPreview = 'gzm_' . $keyType . '_sec_...' . substr($secKey, -4);

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO api_keys (merchant_id, name, key_type, public_key, secret_key_hash, secret_key_preview, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$merchantId, $name, $keyType, $pubKey, $secHash, $secPreview]);

        AuditLogger::log('api_key.created', "Generated {$keyType} API Key {$name}");

        Response::setFlash('success', "API Key created! SAVE YOUR SECRET NOW (it will not be shown again): {$secKey}");
        Response::redirect('/developer');
    }

    public function addWebhook(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $merchantId = Auth::merchantId();
        $url = trim($_POST['url'] ?? '');
        $events = $_POST['events'] ?? ['payment.success'];

        if (empty($url)) {
            Response::setFlash('error', 'Webhook URL is required');
            Response::redirect('/developer');
        }

        $secret = 'whsec_' . bin2hex(random_bytes(16));

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO webhook_endpoints (merchant_id, url, secret, events, status) VALUES (?, ?, ?, ?, 'active')");
        $stmt->execute([$merchantId, $url, $secret, json_encode($events)]);

        AuditLogger::log('webhook.created', "Configured webhook endpoint {$url}");

        Response::setFlash('success', 'Webhook endpoint added successfully!');
        Response::redirect('/developer');
    }

    public function retryWebhook(string $logId): void {
        AuthMiddleware::handle();
        if (WebhookDispatcher::retry((int)$logId)) {
            Response::setFlash('success', 'Webhook retry executed successfully!');
        } else {
            Response::setFlash('error', 'Webhook retry failed.');
        }
        Response::redirect('/developer');
    }
}
