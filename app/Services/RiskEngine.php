<?php

require_once __DIR__ . '/../../config/database.php';

class RiskEngine {

    /**
     * Evaluate transaction risk before payment authorization.
     */
    public static function evaluate(int $merchantId, float $amount, string $customerEmail, string $ipAddress = ''): array {
        $pdo = Database::getConnection();

        $score = 0;
        $reasons = [];

        // Rule 1: High Transaction Amount Threshold
        if ($amount >= 10000.00) {
            $score += 75;
            $reasons[] = 'Transaction amount exceeds GH₵ 10,000 single charge limit.';
        } elseif ($amount >= 5000.00) {
            $score += 25;
            $reasons[] = 'High value charge > GH₵ 5,000.';
        }

        // Rule 2: Customer Velocity Check (Charges in last 1 hour)
        $stmtVel = $pdo->prepare("
            SELECT COUNT(*) 
            FROM transactions t 
            LEFT JOIN customers c ON t.customer_id = c.id 
            WHERE t.merchant_id = ? AND (c.email = ? OR t.ip_address = ?) AND t.created_at >= NOW() - INTERVAL 1 HOUR
        ");
        $stmtVel->execute([$merchantId, $customerEmail, $ipAddress ?: '127.0.0.1']);
        $recentCount = (int)$stmtVel->fetchColumn();

        if ($recentCount >= 5) {
            $score += 50;
            $reasons[] = "High customer velocity: {$recentCount} charges in past hour.";
        } elseif ($recentCount >= 3) {
            $score += 20;
            $reasons[] = "Moderate customer velocity: {$recentCount} charges in past hour.";
        }

        // Rule 3: Merchant KYC & Account Status Check
        $stmtMch = $pdo->prepare("SELECT kyc_status, account_status FROM merchants WHERE id = ?");
        $stmtMch->execute([$merchantId]);
        $mch = $stmtMch->fetch(PDO::FETCH_ASSOC);

        if ($mch && $mch['kyc_status'] === 'verification_pending') {
            $score += 15;
            $reasons[] = 'Merchant KYC verification pending review.';
        }

        // Decision logic
        $decision = 'APPROVE';
        if ($score >= 70) {
            $decision = 'BLOCK';
        } elseif ($score >= 35) {
            $decision = 'REVIEW';
        }

        // Persist Risk Event Log
        $stmtLog = $pdo->prepare("INSERT INTO risk_events (merchant_id, ip_address, risk_score, risk_decision, risk_reasons) VALUES (?, ?, ?, ?, ?)");
        $stmtLog->execute([
            $merchantId,
            $ipAddress ?: ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'),
            $score,
            $decision,
            implode(' | ', $reasons)
        ]);

        return [
            'score' => $score,
            'decision' => $decision,
            'reasons' => $reasons,
            'allowed' => $decision !== 'BLOCK'
        ];
    }
}
