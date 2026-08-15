<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Services/LedgerEngine.php';
require_once __DIR__ . '/../Services/SandboxPaymentGateway.php';

class DisputeController {

    public function index() {
        AuthMiddleware::handle();
        $merchantId = Auth::merchantId();
        $pdo = Database::getConnection();

        $statusFilter = $_GET['status'] ?? 'all';
        $search = trim($_GET['search'] ?? '');

        // Base Query
        $sql = "SELECT d.*, 
                       t.reference AS transaction_reference, t.payment_method, t.created_at AS transaction_date,
                       c.name AS customer_name, c.email AS customer_email
                FROM disputes d
                JOIN transactions t ON d.transaction_id = t.id
                LEFT JOIN customers c ON d.customer_id = c.id
                WHERE d.merchant_id = ?";
        
        $params = [$merchantId];

        if (!empty($statusFilter) && $statusFilter !== 'all') {
            $sql .= " AND d.status = ?";
            $params[] = $statusFilter;
        }

        if (!empty($search)) {
            $sql .= " AND (d.dispute_code LIKE ? OR t.reference LIKE ? OR c.name LIKE ? OR c.email LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY d.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $disputes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate Summary Metrics
        $stmtMetrics = $pdo->prepare("
            SELECT 
                COUNT(*) AS total_disputes,
                SUM(CASE WHEN status = 'needs_response' THEN 1 ELSE 0 END) AS needs_response_count,
                SUM(CASE WHEN status = 'needs_response' THEN amount ELSE 0 END) AS needs_response_amount,
                SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) AS under_review_count,
                SUM(CASE WHEN status = 'won' THEN 1 ELSE 0 END) AS won_count,
                SUM(CASE WHEN status IN ('lost', 'accepted') THEN 1 ELSE 0 END) AS lost_count,
                SUM(amount) AS total_disputed_amount
            FROM disputes 
            WHERE merchant_id = ?
        ");
        $stmtMetrics->execute([$merchantId]);
        $metrics = $stmtMetrics->fetch(PDO::FETCH_ASSOC);

        View::render('disputes/index', [
            'title' => 'Disputes & Chargebacks',
            'activeNav' => 'disputes',
            'disputes' => $disputes,
            'metrics' => $metrics,
            'currentStatus' => $statusFilter,
            'search' => $search
        ]);
    }

    public function show($id) {
        AuthMiddleware::handle();
        $merchantId = Auth::merchantId();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT d.*, 
                   t.reference AS transaction_reference, t.payment_method, t.created_at AS transaction_date, t.metadata AS tx_metadata,
                   c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone
            FROM disputes d
            JOIN transactions t ON d.transaction_id = t.id
            LEFT JOIN customers c ON d.customer_id = c.id
            WHERE d.id = ? AND d.merchant_id = ?
        ");
        $stmt->execute([$id, $merchantId]);
        $dispute = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dispute) {
            header("Location: /disputes");
            exit;
        }

        View::render('disputes/show', [
            'title' => 'Dispute ' . $dispute['dispute_code'],
            'activeNav' => 'disputes',
            'dispute' => $dispute
        ]);
    }

    public function submitEvidence($id) {
        AuthMiddleware::handle();
        $merchantId = Auth::merchantId();
        $pdo = Database::getConnection();

        $evidenceText = trim($_POST['evidence_text'] ?? '');
        $trackingNumber = trim($_POST['tracking_number'] ?? '');

        if (empty($evidenceText)) {
            $_SESSION['flash_error'] = 'Please provide an explanation / evidence details for the dispute.';
            header("Location: /disputes/{$id}");
            exit;
        }

        $fullEvidence = $evidenceText;
        if (!empty($trackingNumber)) {
            $fullEvidence .= "\nFulfillment Proof / Tracking Number: " . $trackingNumber;
        }

        $stmt = $pdo->prepare("
            UPDATE disputes 
            SET evidence_text = ?, status = 'under_review', updated_at = NOW() 
            WHERE id = ? AND merchant_id = ?
        ");
        $stmt->execute([$fullEvidence, $id, $merchantId]);

        $_SESSION['flash_success'] = 'Evidence submitted successfully! The dispute status is now Under Review by the issuing bank.';
        header("Location: /disputes/{$id}");
        exit;
    }

    public function acceptDispute($id) {
        AuthMiddleware::handle();
        $merchantId = Auth::merchantId();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT d.*, t.reference AS transaction_reference 
            FROM disputes d 
            JOIN transactions t ON d.transaction_id = t.id 
            WHERE d.id = ? AND d.merchant_id = ?
        ");
        $stmt->execute([$id, $merchantId]);
        $dispute = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dispute) {
            header("Location: /disputes");
            exit;
        }

        // Process refund reversal via SandboxPaymentGateway
        $gateway = new SandboxPaymentGateway();
        $refundResult = $gateway->refund($dispute['transaction_reference'], (float)$dispute['amount'], 'Merchant accepted chargeback dispute ' . $dispute['dispute_code']);

        // Update Dispute Status
        $upd = $pdo->prepare("
            UPDATE disputes 
            SET status = 'accepted', resolved_at = NOW(), updated_at = NOW() 
            WHERE id = ? AND merchant_id = ?
        ");
        $upd->execute([$id, $merchantId]);

        $_SESSION['flash_success'] = 'Dispute accepted. The disputed amount has been refunded to the cardholder.';
        header("Location: /disputes/{$id}");
        exit;
    }
}
