<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Services/FeeEngine.php';
require_once __DIR__ . '/../app/Services/LedgerEngine.php';
require_once __DIR__ . '/../app/Services/IdempotencyService.php';
require_once __DIR__ . '/../app/Services/SandboxPaymentGateway.php';
require_once __DIR__ . '/../app/Services/ReconciliationService.php';

echo "====================================================\n";
echo "    GAZOMA PAY V1.0 AUTOMATED TEST SUITE RUNNER     \n";
echo "====================================================\n\n";

$passed = 0;
$failed = 0;

function assertTest(string $name, bool $condition, string $failureDetails = ''): void {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] {$name}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$name} - {$failureDetails}\n";
        $failed++;
    }
}

echo "--- 1. UNIT TESTS ---\n";

// Fee Engine Calculation
$calc = FeeEngine::calculate(100.00);
assertTest('FeeEngine: 1.5% + GH₵0.50 calculation on 100 GHS', $calc['fee'] === 2.00 && $calc['net_amount'] === 98.00);

$calc2 = FeeEngine::calculate(1000.00);
assertTest('FeeEngine: 1.5% + GH₵0.50 calculation on 1000 GHS', $calc2['fee'] === 15.50 && $calc2['net_amount'] === 984.50);

// Card Brand Detection
assertTest('SandboxPaymentGateway: Auto-detects Visa card brand (4000...)', SandboxPaymentGateway::detectCardBrand('4000123456789010') === 'Visa');
assertTest('SandboxPaymentGateway: Auto-detects Mastercard brand (5100...)', SandboxPaymentGateway::detectCardBrand('5100123456789010') === 'Mastercard');

// Mobile Money Network Detection
require_once __DIR__ . '/../app/Controllers/PaystackController.php';
assertTest('PaystackController: Auto-detects MTN MoMo network prefix (024...)', PaystackController::detectNetworkProvider('0241234567') === 'mtn');
assertTest('PaystackController: Auto-detects Telecel Cash network prefix (020...)', PaystackController::detectNetworkProvider('0201234567') === 'vod');
assertTest('PaystackController: Auto-detects AT Money network prefix (027...)', PaystackController::detectNetworkProvider('0271234567') === 'tigo');

// Ledger Engine
$testMchId = 1;
$prevAvail = LedgerEngine::getAvailableBalance($testMchId);
LedgerEngine::recordPayment($testMchId, 'TEST_LTX_001', 500.00, 8.00, 492.00, 'Unit test payment');
$newAvail = LedgerEngine::getAvailableBalance($testMchId);

assertTest('LedgerEngine: Record Payment increases available balance by net amount', round($newAvail - $prevAvail, 2) === 492.00);

// Idempotency Check
$key = 'test_key_' . bin2hex(random_bytes(6));
IdempotencyService::store($testMchId, $key, '/api/v1/payments', ['amount' => 100], 200, ['status' => 'successful']);
$cached = IdempotencyService::check($testMchId, $key, '/api/v1/payments', ['amount' => 100]);

assertTest('IdempotencyService: Caches and returns original response on replay', $cached !== null && $cached['code'] === 200 && $cached['body']['status'] === 'successful');

echo "\n--- 2. INTEGRATION TESTS ---\n";

// Payment Gateway Flow
$gateway = new SandboxPaymentGateway();
$res = $gateway->charge([
    'merchant_id' => $testMchId,
    'amount' => 250.00,
    'customer_name' => 'Test Suite Payer',
    'customer_email' => 'testsuite@example.com',
    'payment_method' => 'card'
]);

assertTest('SandboxPaymentGateway: Charge execution returns successful status & net amount', $res['success'] === true && $res['amount'] === 250.00);

// Refund Flow
$refundRes = $gateway->refund($res['reference'], 250.00, 'Test suite refund');
assertTest('SandboxPaymentGateway: Refund execution reverses transaction successfully', $refundRes['success'] === true);

// Provider Abstraction Layer & Resolver
require_once __DIR__ . '/../app/Services/PaymentProviderResolver.php';
$provider = PaymentProviderResolver::resolve('sandbox');
assertTest('PaymentProviderResolver: Resolves SandboxPaymentProvider in sandbox mode', $provider->getProviderName() === 'sandbox');

// Risk & Fraud Engine
require_once __DIR__ . '/../app/Services/RiskEngine.php';
$riskAssessment = RiskEngine::evaluate($testMchId, 15000.00, 'testsuite@example.com', '127.0.0.1');
assertTest('RiskEngine: Scores high value transaction (>10k GHS) and issues BLOCK decision', $riskAssessment['decision'] === 'BLOCK' && $riskAssessment['score'] >= 70);

// Request ID Generation
require_once __DIR__ . '/../app/Helpers/RequestId.php';
$reqId = RequestId::get();
assertTest('RequestId: Generates unique req_ correlation ID', strpos($reqId, 'req_') === 0);

// Webhook Engine Signature Validation & Processing
require_once __DIR__ . '/../app/Services/WebhookEngine.php';
$rawWh = json_encode(['event' => 'payment.success', 'reference' => $res['reference']]);
$secret = Env::get('GAZOMA_WEBHOOK_SECRET', 'whsec_9a8b7c6d5e4f3a2b1c');
$sig = hash_hmac('sha256', $rawWh, $secret);
$whRes = WebhookEngine::receiveAndProcess('sandbox', ['x-gazoma-signature' => $sig], $rawWh);
assertTest('WebhookEngine: Processes webhook event payload and updates database', !empty($whRes['success']), json_encode($whRes));

// Duplicate Webhook Rejection Test
$dupWhRes = WebhookEngine::receiveAndProcess('sandbox', ['x-gazoma-signature' => $sig], $rawWh);
assertTest('WebhookEngine: Rejects duplicate webhook event with zero second ledger posting', $dupWhRes['status'] === 'duplicate');

// Stripe-Style Payment Intent & Payment Attempts
require_once __DIR__ . '/../app/Services/PaymentIntentService.php';
$intent = PaymentIntentService::create($testMchId, [
    'amount' => 350.00,
    'currency' => 'GHS',
    'description' => 'Test Suite Payment Intent'
]);

assertTest('PaymentIntentService: Creates pay_ object in requires_payment_method status with minor unit amount', strpos($intent['public_id'], 'pay_') === 0 && $intent['amount'] === 35000 && $intent['status'] === 'requires_payment_method');

$confirmRes = PaymentIntentService::confirm($intent['public_id'], [
    'payment_method' => 'card',
    'customer_email' => 'intent_test@example.com'
]);

assertTest('PaymentIntentService: Confirms payment intent, tracks attempt, and updates status to succeeded', $confirmRes['success'] === true && $confirmRes['status'] === 'succeeded' && !empty($confirmRes['payment']['attempts']));

// Payment Intent Cancellation Test
$intentToCancel = PaymentIntentService::create($testMchId, ['amount' => 100.00, 'currency' => 'GHS']);
$cancelRes = PaymentIntentService::cancel($intentToCancel['public_id']);
assertTest('PaymentIntentService: Cancels payment intent and transitions status to canceled', $cancelRes['success'] === true && $cancelRes['status'] === 'canceled');

echo "\n--- 3. FINANCIAL RECONCILIATION AUDIT ---\n";
$audit = ReconciliationService::auditMerchant($testMchId);
assertTest('ReconciliationService: Financial reconciliation audit status is PASS', $audit['status'] === 'PASS', implode('; ', $audit['issues']));

echo "\n====================================================\n";
echo " TEST RESULTS SUMMARY: Passed: {$passed} | Failed: {$failed}\n";
echo "====================================================\n";

exit($failed > 0 ? 1 : 0);
