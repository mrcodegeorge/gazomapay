<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Services/LedgerEngine.php';

echo "=== Gazoma Pay Hardened Database Seeder ===\n";

try {
    $pdo = Database::getConnection();
    
    echo "[1/4] Applying schema SQL...\n";
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    
    // Split multi-statement SQL by semicolon for reliable execution
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmtSql) {
        if (!empty($stmtSql)) {
            $pdo->exec($stmtSql);
        }
    }
    echo "Schema applied successfully.\n";

    echo "[1b/4] Applying migration files...\n";
    $migrationFiles = glob(__DIR__ . '/migrations/*.sql');
    sort($migrationFiles);
    foreach ($migrationFiles as $migFile) {
        $migSql = file_get_contents($migFile);
        $migStatements = array_filter(array_map('trim', explode(';', $migSql)));
        foreach ($migStatements as $mStmt) {
            if (!empty($mStmt)) {
                $pdo->exec($mStmt);
            }
        }
        echo "Applied migration: " . basename($migFile) . "\n";
    }

    echo "[2/4] Seeding Merchants & Users...\n";
    $passHash = password_hash('password123', PASSWORD_BCRYPT);
    
    // Merchant: Gazoma Tech
    $stmt = $pdo->prepare("INSERT INTO merchants (uuid, merchant_id, name, legal_name, trading_name, business_registration_number, business_type, email, phone, logo, country, currency, timezone, address, environment, available_balance, pending_balance, settled_balance, onboarding_completed, onboarding_step, kyc_status, account_status, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 4, ?, ?, ?)");
    $stmt->execute([
        'mch_892374829374',
        'GZM_123456',
        'Gazoma Tech',
        'Gazoma Tech Ghana Limited',
        'Gazoma Tech',
        'CS-892019284',
        'limited_company',
        'contact@gazomatech.com',
        '+233 24 123 4567',
        '/assets/images/logo.png',
        'Ghana',
        'GHS',
        'Africa/Accra',
        '15 Independence Avenue, Ridge, Accra',
        'live',
        28560.00,
        4250.00,
        93750.00,
        'approved',
        'active',
        'active'
    ]);
    $merchantId = $pdo->lastInsertId();

    // Owner User: John Mensah
    $stmtUser = $pdo->prepare("INSERT INTO users (merchant_id, uuid, name, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmtUser->execute([
        $merchantId,
        'usr_john_mensah_001',
        'John Mensah',
        'admin@gazomapay.com',
        $passHash,
        'admin',
        'active'
    ]);

    // Platform Super Admin
    $stmtUser->execute([
        $merchantId,
        'usr_superadmin_000',
        'Gazoma System Admin',
        'superadmin@gazomapay.com',
        $passHash,
        'platform_admin',
        'active'
    ]);

    echo "[3/4] Seeding Customers, Payment Links, Invoices...\n";
    
    // Customers
    $customersData = [
        ['Ama Serwaa', 'ama.serwaa@example.com', '+233 20 111 2233', 18, 4850.00, 17, 1],
        ['Kofi Mensah', 'kofi.mensah@example.com', '+233 24 999 8877', 12, 3200.00, 12, 0],
        ['Comfort Stores', 'info@comfortstores.com', '+233 30 555 4433', 45, 18900.00, 42, 3],
        ['Gloria Adjei', 'gloria.adjei@example.com', '+233 55 333 2211', 8, 1250.00, 8, 0],
        ['Smart Gadgets', 'sales@smartgadgets.gh', '+233 27 888 7766', 30, 14200.00, 29, 1],
        ['Nana Yaw', 'nana.yaw@example.com', '+233 24 444 5566', 5, 960.00, 4, 1],
        ['Adwoa Ansubonteng', 'adwoa.a@example.com', '+233 50 123 9876', 14, 3800.00, 14, 0],
    ];

    $customerIds = [];
    $stmtCust = $pdo->prepare("INSERT INTO customers (merchant_id, uuid, name, email, phone, country, total_transactions, total_spending, successful_payments, failed_payments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($customersData as $idx => $c) {
        $uuid = 'cst_' . sprintf('%04d', $idx + 1);
        $stmtCust->execute([$merchantId, $uuid, $c[0], $c[1], $c[2], 'Ghana', $c[3], $c[4], $c[5], $c[6]]);
        $customerIds[$c[0]] = $pdo->lastInsertId();
    }

    // Payment Links
    $paymentLinksData = [
        ['iPhone 15 Payment', 'Official iPhone 15 pre-order link', 6500.00, 12, 0, 'active', 'PL_1234567890', '2024-05-31 10:00:00'],
        ['Laptop Payment', 'High performance workstation bundle', 8000.00, 7, 0, 'active', 'PL_8829304918', '2024-05-30 14:20:00'],
        ['Consulting Fee', 'Business strategy consultation retainer', 1500.00, 23, 0, 'active', 'PL_4829103948', '2024-05-29 11:30:00'],
        ['Event Ticket', 'Tech Summit Accra 2024 VIP pass', 150.00, 132, 500, 'active', 'PL_9102837465', '2024-05-28 09:15:00'],
        ['Course Payment', 'Full-stack software engineering bootcamp', 600.00, 45, 100, 'inactive', 'PL_3019283746', '2024-05-27 16:45:00'],
    ];

    $linkIds = [];
    $stmtPL = $pdo->prepare("INSERT INTO payment_links (merchant_id, token, name, description, amount, currency, usage_count, max_uses, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($paymentLinksData as $pl) {
        $stmtPL->execute([$merchantId, $pl[6], $pl[0], $pl[1], $pl[2], 'GHS', $pl[3], $pl[4], $pl[5], $pl[7]]);
        $linkIds[$pl[0]] = $pdo->lastInsertId();
    }

    // 156 Views for iPhone 15 Link
    $iphoneLinkId = $linkIds['iPhone 15 Payment'];
    $stmtViews = $pdo->prepare("INSERT INTO payment_link_views (payment_link_id, ip_address, user_agent) VALUES (?, ?, ?)");
    for ($i = 0; $i < 156; $i++) {
        $stmtViews->execute([$iphoneLinkId, '197.251.14.' . ($i % 250), 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)']);
    }

    echo "[4/4] Seeding Transactions, Financial Ledger & Webhook Logs...\n";

    // 1. Initial Historical Earnings Batch to match Mockup total volume (126,560.00 GHS)
    $stmtTx = $pdo->prepare("INSERT INTO transactions (reference, event_id, merchant_id, customer_id, payment_link_id, amount, fee, net_amount, currency, payment_method, provider, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $historicalGross = 126560.00;
    $historicalFee = round($historicalGross * 0.015, 2);
    $historicalNet = $historicalGross - $historicalFee;

    // Post Historical Earnings to Ledger
    LedgerEngine::recordPayment($merchantId, 'GZM_HIST_001', $historicalGross, $historicalFee, $historicalNet, "Historical platform processing earnings");

    // Exact Transactions visible in mockup UI
    $exactMockupTx = [
        ['GZM_00012345', 'Ama Serwaa', 200.00, 'successful', '2024-05-31 10:24:00', 'card'],
        ['GZM_00012344', 'Kofi Mensah', 150.00, 'successful', '2024-05-31 09:15:00', 'mobile_money'],
        ['GZM_00012343', 'Comfort Stores', 500.00, 'pending', '2024-05-30 16:45:00', 'bank_transfer'],
        ['GZM_00012342', 'Gloria Adjei', 100.00, 'successful', '2024-05-30 14:31:00', 'card'],
        ['GZM_00012341', 'Smart Gadgets', 670.00, 'successful', '2024-05-29 11:05:00', 'mobile_money'],
        ['GZM_00012340', 'Nana Yaw', 320.00, 'failed', '2024-05-29 09:20:00', 'card'],
        ['GZM_00012339', 'Adwoa Ansubonteng', 410.00, 'successful', '2024-05-28 15:15:00', 'mobile_money'],
    ];

    foreach ($exactMockupTx as $tx) {
        $cId = $customerIds[$tx[1]] ?? null;
        $fee = round($tx[2] * 0.015 + 0.50, 2);
        $net = $tx[2] - $fee;
        $evtId = 'evt_' . bin2hex(random_bytes(10));
        $stmtTx->execute([$tx[0], $evtId, $merchantId, $cId, null, $tx[2], $fee, $net, 'GHS', $tx[5], 'Sandbox Gateway', $tx[3], $tx[4]]);
    }

    // Settlements matching mockup
    $stmtSet = $pdo->prepare("INSERT INTO settlements (reference, merchant_id, gross_amount, fee, net_amount, bank_name, account_number, account_name, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Settlement 1 (50,000 GHS)
    $stmtSet->execute(['SET_89230192', $merchantId, 50000.00, 250.00, 49750.00, 'GCB Bank Ghana', '1011129384728', 'Gazoma Tech Ltd', 'completed', '2024-05-25 14:00:00']);
    LedgerEngine::recordSettlementRequest($merchantId, 'SET_89230192', 50000.00);
    LedgerEngine::recordSettlementCompletion($merchantId, 'SET_89230192', 50000.00, 250.00, 49750.00);

    // Settlement 2 (44,000 GHS)
    $stmtSet->execute(['SET_89230193', $merchantId, 44000.00, 220.00, 43780.00, 'Stanbic Bank Ghana', '9040001827364', 'Gazoma Tech Ltd', 'completed', '2024-05-15 10:30:00']);
    LedgerEngine::recordSettlementRequest($merchantId, 'SET_89230193', 44000.00);
    LedgerEngine::recordSettlementCompletion($merchantId, 'SET_89230193', 44000.00, 220.00, 43780.00);

    // Settlement 3 (4,250 GHS Pending)
    $stmtSet->execute(['SET_89230194', $merchantId, 4250.00, 21.25, 4228.75, 'GCB Bank Ghana', '1011129384728', 'Gazoma Tech Ltd', 'pending', '2024-05-31 16:00:00']);
    LedgerEngine::recordSettlementRequest($merchantId, 'SET_89230194', 4250.00);

    // Update stored balances to match exact Ledger Engine calculations
    $realAvail = LedgerEngine::getAvailableBalance($merchantId);
    $realPending = LedgerEngine::getPendingBalance($merchantId);
    $realSettled = LedgerEngine::getSettledBalance($merchantId);

    $updMchBal = $pdo->prepare("UPDATE merchants SET available_balance = ?, pending_balance = ?, settled_balance = ? WHERE id = ?");
    $updMchBal->execute([$realAvail, $realPending, $realSettled, $merchantId]);

    // Invoices
    $stmtInv = $pdo->prepare("INSERT INTO invoices (merchant_id, customer_id, invoice_number, subtotal, tax, discount, total, status, due_date, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtInv->execute([$merchantId, $customerIds['Ama Serwaa'], 'INV-2024-001', 1200.00, 0.00, 0.00, 1200.00, 'paid', '2024-06-15', 'Thank you for your business.']);
    $inv1Id = $pdo->lastInsertId();
    
    $stmtInvItem = $pdo->prepare("INSERT INTO invoice_items (invoice_id, description, quantity, unit_price, amount) VALUES (?, ?, ?, ?, ?)");
    $stmtInvItem->execute([$inv1Id, 'Custom Web Software Development Services', 1, 1200.00, 1200.00]);

    // API Keys
    $stmtApi = $pdo->prepare("INSERT INTO api_keys (merchant_id, name, key_type, public_key, secret_key_hash, secret_key_preview, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmtApi->execute([$merchantId, 'Default Live Key', 'live', 'gzm_live_pub_9a8b7c6d5e4f3a2b', password_hash('gzm_live_sec_8819203918237491', PASSWORD_BCRYPT), 'gzm_live_sec_...7491', 'active']);
    $stmtApi->execute([$merchantId, 'Testing Key', 'test', 'gzm_test_pub_1a2b3c4d5e6f7a8b', password_hash('gzm_test_sec_1122334455667788', PASSWORD_BCRYPT), 'gzm_test_sec_...7788', 'active']);

    // Webhook Endpoints & Logs
    $stmtWh = $pdo->prepare("INSERT INTO webhook_endpoints (merchant_id, url, secret, events, status) VALUES (?, ?, ?, ?, ?)");
    $stmtWh->execute([$merchantId, 'https://gazomatech.com/api/webhooks/payments', 'whsec_90192837465', json_encode(['payment.success', 'payment.failed', 'settlement.completed']), 'active']);
    $whId = $pdo->lastInsertId();

    $stmtWhLog = $pdo->prepare("INSERT INTO webhook_logs (merchant_id, endpoint_id, event_id, event_type, payload, signature, response_code, response_body, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtWhLog->execute([$merchantId, $whId, 'evt_9018237465', 'payment.success', json_encode(['event' => 'payment.success', 'data' => ['reference' => 'GZM_00012345', 'amount' => 200.00]]), 't=1723639200,v1=9f8a7b6c5d4e3f2a', 200, '{"received":true}', 'delivered']);

    // Seed Sample Disputes
    $stmtDsp = $pdo->prepare("INSERT INTO disputes (dispute_code, merchant_id, transaction_id, customer_id, amount, reason, evidence_text, status, due_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmtTxFetch = $pdo->prepare("SELECT id, reference, amount, customer_id FROM transactions WHERE reference IN ('GZM_00012345', 'GZM_00012341', 'GZM_00012339')");
    $stmtTxFetch->execute();
    $txs = $stmtTxFetch->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($txs)) {
        // Dispute 1: Needs Response
        $stmtDsp->execute(['DSP_89201923', $merchantId, $txs[0]['id'], $txs[0]['customer_id'], 200.00, 'unauthorized_charge', null, 'needs_response', date('Y-m-d', strtotime('+7 days')), '2024-06-01 09:00:00']);

        // Dispute 2: Under Review
        if (isset($txs[1])) {
            $stmtDsp->execute(['DSP_89201924', $merchantId, $txs[1]['id'], $txs[1]['customer_id'], 670.00, 'product_not_received', 'Item was delivered via Waybill #WB-9920. Customer signed receipt.', 'under_review', date('Y-m-d', strtotime('+12 days')), '2024-05-29 14:00:00']);
        }

        // Dispute 3: Won
        if (isset($txs[2])) {
            $stmtDsp->execute(['DSP_89201925', $merchantId, $txs[2]['id'], $txs[2]['customer_id'], 410.00, 'fraudulent', 'Verified 3DS OTP transaction log provided to issuing bank.', 'won', '2024-05-20', '2024-05-18 11:30:00']);
        }
    }

    // Seed Subscription Plans
    $stmtSubPlan = $pdo->prepare("INSERT INTO subscription_plans (merchant_id, name, description, amount, currency, billing_interval, trial_days, status) VALUES (?, ?, ?, ?, 'GHS', ?, ?, 'active')");
    $stmtSubPlan->execute([$merchantId, 'Starter Monthly Tier', 'Starter subscription plan for small businesses', 99.00, 'monthly', 7]);
    $plan1Id = $pdo->lastInsertId();

    $stmtSubPlan->execute([$merchantId, 'Pro Business Plan', 'Full feature access with high volume discounts', 299.00, 'monthly', 14]);
    $plan2Id = $pdo->lastInsertId();

    $stmtSubPlan->execute([$merchantId, 'Enterprise Annual Tier', 'Annual enterprise SLA agreement & priority support', 2999.00, 'yearly', 30]);
    $plan3Id = $pdo->lastInsertId();

    // Seed Active Subscriptions
    $stmtSub = $pdo->prepare("INSERT INTO subscriptions (merchant_id, customer_id, plan_id, status, next_billing_date) VALUES (?, ?, ?, ?, ?)");
    if (!empty($customerIds['Ama Serwaa'])) {
        $stmtSub->execute([$merchantId, $customerIds['Ama Serwaa'], $plan1Id, 'active', date('Y-m-d', strtotime('+25 days'))]);
    }
    if (!empty($customerIds['Kofi Mensah'])) {
        $stmtSub->execute([$merchantId, $customerIds['Kofi Mensah'], $plan2Id, 'active', date('Y-m-d', strtotime('+14 days'))]);
    }
    if (!empty($customerIds['Comfort Stores'])) {
        $stmtSub->execute([$merchantId, $customerIds['Comfort Stores'], $plan3Id, 'active', date('Y-m-d', strtotime('+310 days'))]);
    }

    echo "=== Gazoma Pay Hardened Database Seeded Successfully! ===\n";

} catch (Exception $e) {
    die("Seeding Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
}
