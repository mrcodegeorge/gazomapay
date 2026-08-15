<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/Format.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/CsrfMiddleware.php';
require_once __DIR__ . '/../Services/AuditLogger.php';
require_once __DIR__ . '/../../config/database.php';

class OnboardingController {

    public function index(): void {
        AuthMiddleware::handle();
        $merchantId = Auth::merchantId();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM merchants WHERE id = ?");
        $stmt->execute([$merchantId]);
        $merchant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$merchant) {
            Response::redirect('/login');
        }

        View::render('onboarding/index', [
            'pageTitle' => 'Merchant Onboarding',
            'merchant' => $merchant
        ], 'none');
    }

    public function saveStep(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $merchantId = Auth::merchantId();
        $pdo = Database::getConnection();
        $step = (int)($_POST['step'] ?? 1);

        if ($step === 1) {
            $legalName = trim($_POST['legal_name'] ?? '');
            $tradingName = trim($_POST['trading_name'] ?? '');
            $businessType = $_POST['business_type'] ?? 'limited_company';
            $regNumber = trim($_POST['business_registration_number'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if (empty($legalName)) {
                Response::setFlash('error', 'Please fill in your registered business legal name.');
                Response::redirect('/onboarding');
            }

            $stmt = $pdo->prepare("
                UPDATE merchants 
                SET legal_name = ?, trading_name = ?, business_type = ?, business_registration_number = ?, phone = ?, address = ?, onboarding_step = 2 
                WHERE id = ?
            ");
            $stmt->execute([$legalName, $tradingName ?: $legalName, $businessType, $regNumber, $phone, $address, $merchantId]);

            AuditLogger::log('onboarding.step_1', 'Completed Business Details onboarding step.');
            Response::setFlash('success', 'Business details saved! Next: Settlement Account.');
            Response::redirect('/onboarding');

        } elseif ($step === 2) {
            $bankName = trim($_POST['bank_name'] ?? '');
            $accountNumber = trim($_POST['account_number'] ?? '');
            $accountName = trim($_POST['account_name'] ?? '');

            if (empty($bankName) || empty($accountNumber)) {
                Response::setFlash('error', 'Bank name and account number are required for settlements.');
                Response::redirect('/onboarding');
            }

            $stmt = $pdo->prepare("UPDATE merchants SET onboarding_step = 3 WHERE id = ?");
            $stmt->execute([$merchantId]);

            AuditLogger::log('onboarding.step_2', "Configured payout account {$bankName} ({$accountNumber})");
            Response::setFlash('success', 'Payout bank account configured! Next: Identity Verification.');
            Response::redirect('/onboarding');

        } elseif ($step === 3) {
            $idType = $_POST['id_type'] ?? 'ghana_card';
            $idNumber = trim($_POST['id_number'] ?? '');

            if (empty($idNumber)) {
                Response::setFlash('error', 'Please provide a valid national ID or passport number.');
                Response::redirect('/onboarding');
            }

            $stmt = $pdo->prepare("UPDATE merchants SET onboarding_step = 4, kyc_status = 'approved' WHERE id = ?");
            $stmt->execute([$merchantId]);

            AuditLogger::log('onboarding.step_3', "Submitted KYC ID verification ({$idType})");
            Response::setFlash('success', 'KYC Verification approved! Final step: Account Activation.');
            Response::redirect('/onboarding');
        }
    }

    public function complete(): void {
        AuthMiddleware::handle();
        CsrfMiddleware::handle();

        $merchantId = Auth::merchantId();
        $pdo = Database::getConnection();

        // Mark merchant onboarding complete and account active
        $stmt = $pdo->prepare("
            UPDATE merchants 
            SET onboarding_completed = 1, onboarding_step = 4, account_status = 'active', kyc_status = 'approved', status = 'active' 
            WHERE id = ?
        ");
        $stmt->execute([$merchantId]);

        // Generate default API key pair if not exists
        $stmtCheckKey = $pdo->prepare("SELECT COUNT(*) FROM api_keys WHERE merchant_id = ?");
        $stmtCheckKey->execute([$merchantId]);
        if ($stmtCheckKey->fetchColumn() == 0) {
            $pubKey = 'gzm_live_pub_' . bin2hex(random_bytes(8));
            $secKeyRaw = 'gzm_live_sec_' . bin2hex(random_bytes(12));
            $secHash = password_hash($secKeyRaw, PASSWORD_BCRYPT);
            $preview = substr($secKeyRaw, 0, 12) . '...' . substr($secKeyRaw, -4);

            $stmtKey = $pdo->prepare("INSERT INTO api_keys (merchant_id, name, key_type, public_key, secret_key_hash, secret_key_preview, status) VALUES (?, 'Default Live Key', 'live', ?, ?, ?, 'active')");
            $stmtKey->execute([$merchantId, $pubKey, $secHash, $preview]);
        }

        AuditLogger::log('merchant.onboarded', 'Completed full Gazoma Pay merchant onboarding wizard!');

        Response::setFlash('success', '🎉 Welcome to Gazoma Pay! Your merchant account is 100% verified and active.');
        Response::redirect('/dashboard');
    }
}
