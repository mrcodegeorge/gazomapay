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

        // Team members
        $stmtUsers = $pdo->prepare("SELECT * FROM users WHERE merchant_id = ? ORDER BY created_at ASC");
        $stmtUsers->execute([$merchantId]);
        $team = $stmtUsers->fetchAll();

        View::render('settings/index', [
            'pageTitle' => 'Settings',
            'pageSubtitle' => 'Configure business profile, account security, and team management.',
            'user' => Auth::user(),
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

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE merchants SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $address, $merchantId]);

        AuditLogger::log('settings.profile_updated', 'Updated merchant business profile settings');

        Response::setFlash('success', 'Business profile updated successfully!');
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

        Response::setFlash('success', 'Team member added successfully!');
        Response::redirect('/settings');
    }
}
