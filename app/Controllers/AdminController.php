<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/Format.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../config/database.php';

class AdminController {
    public function index(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();

        $stmtMch = $pdo->query("SELECT * FROM merchants ORDER BY created_at DESC");
        $merchants = $stmtMch->fetchAll();

        $stmtLogs = $pdo->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 20");
        $systemLogs = $stmtLogs->fetchAll();

        View::render('admin/index', [
            'pageTitle' => 'Platform Administration',
            'pageSubtitle' => 'Superadmin management for Gazoma Pay platform operators.',
            'merchants' => $merchants,
            'systemLogs' => $systemLogs
        ]);
    }
}
