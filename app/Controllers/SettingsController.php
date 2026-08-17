<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/Format.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/CsrfMiddleware.php';
require_once __DIR__ . '/../Services/AuditLogger.php';
require_once __DIR__ . '/../../config/database.php';

class SettingsController {

    public function index(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();

        // 1. Fetch full Merchant Details
        $stmtMch = $pdo->prepare("SELECT * FROM merchants WHERE id = ?");
        $stmtMch->execute([$merchantId]);
        $merchant = $stmtMch->fetch(PDO::FETCH_ASSOC) ?: [];

        // 2. Fetch Team Members
        $stmtUsers = $pdo->prepare("SELECT * FROM users WHERE merchant_id = ? ORDER BY created_at ASC");
        $stmtUsers->execute([$merchantId]);
        $team = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

        View::render('settings/index', [
            'pageTitle' => 'Account & Business Settings',
            'pageSubtitle' => 'Configure merchant profile, API keys, webhook endpoints, team roles, and account security.',
            'user' => Auth::user(),
            'merchant' => $merchant,
            'team' => $team
        ]);
    }

    public function updateProfile(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $merchantId = Auth::merchantId();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $webhookUrl = trim($_POST['webhook_url'] ?? '');
        $businessType = trim($_POST['business_type'] ?? 'limited_company');

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE merchants SET name = ?, email = ?, phone = ?, address = ?, webhook_url = ?, business_type = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $address, $webhookUrl, $businessType, $merchantId]);

        AuditLogger::log('settings.profile_updated', 'Updated merchant business profile & webhook settings');

        Response::setFlash('success', 'Business profile settings updated successfully!');
        Response::redirect('/settings');
    }

    public function addTeamMember(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $merchantId = Auth::merchantId();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'admin';
        $passHash = password_hash('password123', PASSWORD_BCRYPT);
        $uuid = 'usr_' . bin2hex(random_bytes(6));

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO users (merchant_id, uuid, name, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$merchantId, $uuid, $name, $email, $passHash, $role]);

        AuditLogger::log('team.user_added', "Added staff member {$name} with role {$role}");

        Response::setFlash('success', "Team member {$name} added successfully!");
        Response::redirect('/settings');
    }

    public function updatePassword(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $user = Auth::user();
        $currentPass = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (strlen($newPass) < 8) {
            Response::setFlash('error', 'New password must be at least 8 characters long.');
            Response::redirect('/settings');
            return;
        }

        if ($newPass !== $confirmPass) {
            Response::setFlash('error', 'New password and confirmation do not match.');
            Response::redirect('/settings');
            return;
        }

        $pdo = Database::getConnection();
        $stmtFetch = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmtFetch->execute([$user['id']]);
        $passHash = $stmtFetch->fetchColumn();

        if (!$passHash || !password_verify($currentPass, $passHash)) {
            Response::setFlash('error', 'Current password entered is incorrect.');
            Response::redirect('/settings');
            return;
        }

        $newHash = password_hash($newPass, PASSWORD_BCRYPT);
        $stmtUpd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmtUpd->execute([$newHash, $user['id']]);

        AuditLogger::log('user.password_updated', 'Updated account security password');

        Response::setFlash('success', 'Account security password updated successfully!');
        Response::redirect('/settings');
    }

    public function rotateApiKeys(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $merchantId = Auth::merchantId();
        $pdo = Database::getConnection();

        $newLivePub = 'pk_live_' . bin2hex(random_bytes(12));
        $newLiveSec = 'sk_live_' . bin2hex(random_bytes(18));
        $newTestPub = 'pk_test_' . bin2hex(random_bytes(12));
        $newTestSec = 'sk_test_' . bin2hex(random_bytes(18));
        $newWhSecret = 'whsec_' . bin2hex(random_bytes(16));

        $stmt = $pdo->prepare("UPDATE merchants SET live_public_key = ?, live_secret_key = ?, test_public_key = ?, test_secret_key = ?, webhook_secret = ? WHERE id = ?");
        $stmt->execute([$newLivePub, $newLiveSec, $newTestPub, $newTestSec, $newWhSecret, $merchantId]);

        AuditLogger::log('api_keys.rotated', 'Regenerated merchant API keys and webhook signing secret');

        Response::setFlash('success', 'API Keys and Webhook Secret rotated successfully!');
        Response::redirect('/settings');
    }

    public function toggleEnvironment(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $merchantId = Auth::merchantId();
        $pdo = Database::getConnection();

        $stmtCurr = $pdo->prepare("SELECT environment FROM merchants WHERE id = ?");
        $stmtCurr->execute([$merchantId]);
        $currEnv = $stmtCurr->fetchColumn();

        $targetEnv = ($currEnv === 'live') ? 'test' : 'live';

        $stmtUpd = $pdo->prepare("UPDATE merchants SET environment = ? WHERE id = ?");
        $stmtUpd->execute([$targetEnv, $merchantId]);

        // Refresh Auth Session Data
        unset($_SESSION['user_data']);

        AuditLogger::log('merchant.environment_switched', "Switched merchant environment mode from {$currEnv} to {$targetEnv}");

        Response::setFlash('success', "Environment mode successfully switched to " . strtoupper($targetEnv) . " MODE!");
        
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/dashboard';
        Response::redirect($redirectUrl);
    }
}
