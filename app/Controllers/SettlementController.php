<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/Format.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/CsrfMiddleware.php';
require_once __DIR__ . '/../Services/AuditLogger.php';
require_once __DIR__ . '/../../config/database.php';

class SettlementController {
    public function index(): void {
        AuthMiddleware::handle();
        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();

        $stmtMch = $pdo->prepare("SELECT available_balance, pending_balance, settled_balance FROM merchants WHERE id = ?");
        $stmtMch->execute([$merchantId]);
        $mch = $stmtMch->fetch();

        $stmtSet = $pdo->prepare("SELECT * FROM settlements WHERE merchant_id = ? ORDER BY created_at DESC");
        $stmtSet->execute([$merchantId]);
        $settlements = $stmtSet->fetchAll();

        View::render('settlements/index', [
            'pageTitle' => 'Settlements',
            'pageSubtitle' => 'Manage automated payouts and withdrawal requests to your bank account.',
            'merchant' => $mch,
            'settlements' => $settlements
        ]);
    }

    public function request(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $pdo = Database::getConnection();
        $merchantId = Auth::merchantId();
        $amount = (float)($_POST['amount'] ?? 0);
        $bankInfo = trim($_POST['bank_name'] ?? 'GCB Bank Ghana - 1011129384728');

        $stmtMch = $pdo->prepare("SELECT available_balance FROM merchants WHERE id = ?");
        $stmtMch->execute([$merchantId]);
        $available = (float)$stmtMch->fetchColumn();

        if ($amount <= 0 || $amount > $available) {
            Response::setFlash('error', 'Invalid withdrawal amount requested');
            Response::redirect('/settlements');
        }

        $fee = round($amount * 0.005, 2); // 0.5% settlement fee
        $net = $amount - $fee;
        $ref = 'SET_' . sprintf('%08d', rand(100000, 99999999));

        $bankParts = explode(' - ', $bankInfo, 2);
        $bankName = $bankParts[0] ?? 'GCB Bank Ghana';
        $accNum = $bankParts[1] ?? '1011129384728';

        // Create Settlement Record
        $stmtIns = $pdo->prepare("INSERT INTO settlements (reference, merchant_id, gross_amount, fee, net_amount, currency, bank_name, account_number, account_name, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmtIns->execute([$ref, $merchantId, $amount, $fee, $net, 'GHS', $bankName, $accNum, 'Gazoma Tech Ltd']);

        // Deduct from Available Balance and add to Pending Balance
        $updMch = $pdo->prepare("UPDATE merchants SET available_balance = available_balance - ?, pending_balance = pending_balance + ? WHERE id = ?");
        $updMch->execute([$amount, $amount, $merchantId]);

        AuditLogger::log('settlement.requested', "Requested settlement {$ref} for GH₵ {$amount}");

        Response::setFlash('success', 'Settlement requested successfully! Reference: ' . $ref);
        Response::redirect('/settlements');
    }
}
