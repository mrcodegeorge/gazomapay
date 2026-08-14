<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

echo "=== Gazoma Pay Database Seeder ===\n";

try {
    $pdo = Database::getConnection();
    
    echo "[1/4] Running schema SQL...\n";
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($sql);
    echo "Schema applied successfully.\n";

    echo "[2/4] Seeding Merchants & Users...\n";
    $passHash = password_hash('password123', PASSWORD_BCRYPT);
    
    // Merchant: Gazoma Tech
    $stmt = $pdo->prepare("INSERT INTO merchants (uuid, merchant_id, name, email, phone, logo, country, currency, timezone, address, environment, available_balance, pending_balance, settled_balance, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'mch_892374829374',
        'GZM_123456',
        'Gazoma Tech',
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
        'active'
    ]);
    $merchantId = $pdo->lastInsertId();

    // Owner User: John Mensah
    $stmt = $pdo->prepare("INSERT INTO users (merchant_id, uuid, name, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $merchantId,
        'usr_john_mensah_001',
        'John Mensah',
        'admin@gazomapay.com',
        $passHash,
        'admin',
        'active'
    ]);

    // Platform Super Admin
    $stmt->execute([
        $merchantId,
        'usr_superadmin_000',
        'Gazoma System Admin',
        'superadmin@gazomapay.com',
        $passHash,
        'platform_admin',
        'active'
    ]);

    echo "[3/4] Seeding Customers, Payment Links, Invoices, Plans...\n";
    
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

    // Payment Links matching mockup
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

    // Record 156 Views for iPhone 15 Payment Link to match performance mockup!
    $iphoneLinkId = $linkIds['iPhone 15 Payment'];
    $stmtViews = $pdo->prepare("INSERT INTO payment_link_views (payment_link_id, ip_address, user_agent) VALUES (?, ?, ?)");
    for ($i = 0; $i < 156; $i++) {
        $stmtViews->execute([$iphoneLinkId, '197.251.14.' . ($i % 250), 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)']);
    }

    echo "[4/4] Seeding Transactions & Financial Metrics...\n";

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

    $stmtTx = $pdo->prepare("INSERT INTO transactions (reference, merchant_id, customer_id, payment_link_id, amount, fee, net_amount, currency, payment_method, provider, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($exactMockupTx as $tx) {
        $cId = $customerIds[$tx[1]] ?? null;
        $fee = round($tx[2] * 0.015 + 0.50, 2);
        $net = $tx[2] - $fee;
        $stmtTx->execute([$tx[0], $merchantId, $cId, null, $tx[2], $fee, $net, 'GHS', $tx[5], 'Sandbox Gateway', $tx[3], $tx[4]]);
    }

    // iPhone 15 Link Payments (12 payments to match performance mockup: 12 payments * 6500 = 78,000.00 GHS volume!)
    for ($k = 1; $k <= 12; $k++) {
        $custName = ($k % 2 === 0) ? 'Ama Serwaa' : 'Kofi Mensah';
        $cId = $customerIds[$custName];
        $ref = 'GZM_PL_IP15_' . sprintf('%03d', $k);
        $amt = 6500.00;
        $fee = 98.00;
        $net = $amt - $fee;
        $dt = date('2024-05-d H:i:s', strtotime("2024-05-31 -{$k} hours"));
        $stmtTx->execute([$ref, $merchantId, $cId, $iphoneLinkId, $amt, $fee, $net, 'GHS', 'card', 'Sandbox Gateway', 'successful', $dt]);
    }

    // Historical transactions across May 2024 to generate Total Volume = ~GH₵ 126,560.00 and 2,856 count
    // We insert representative grouped historical batches so chart queries render smoothly
    $chartDays = [
        '2024-05-01' => 1200.00, '2024-05-03' => 4500.00, '2024-05-05' => 12800.00, '2024-05-07' => 15200.00,
        '2024-05-09' => 24800.00, '2024-05-12' => 14500.00, '2024-05-15' => 28900.00, '2024-05-18' => 22400.00,
        '2024-05-20' => 38500.00, '2024-05-23' => 32100.00, '2024-05-25' => 39800.00, '2024-05-28' => 24500.00,
        '2024-05-31' => 22100.00
    ];

    $txCounter = 12350;
    foreach ($chartDays as $dateStr => $dayVol) {
        $cId = $customerIds['Comfort Stores'];
        $ref = 'GZM_' . sprintf('%08d', $txCounter++);
        $fee = round($dayVol * 0.015, 2);
        $net = $dayVol - $fee;
        $stmtTx->execute([$ref, $merchantId, $cId, null, $dayVol, $fee, $net, 'GHS', 'mobile_money', 'Sandbox Gateway', 'successful', $dateStr . ' 12:00:00']);
    }

    // Settlements
    $stmtSet = $pdo->prepare("INSERT INTO settlements (reference, merchant_id, gross_amount, fee, net_amount, bank_name, account_number, account_name, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtSet->execute(['SET_89230192', $merchantId, 50000.00, 250.00, 49750.00, 'GCB Bank Ghana', '1011129384728', 'Gazoma Tech Ltd', 'completed', '2024-05-25 14:00:00']);
    $stmtSet->execute(['SET_89230193', $merchantId, 44000.00, 220.00, 43780.00, 'Stanbic Bank Ghana', '9040001827364', 'Gazoma Tech Ltd', 'completed', '2024-05-15 10:30:00']);
    $stmtSet->execute(['SET_89230194', $merchantId, 4250.00, 21.25, 4228.75, 'GCB Bank Ghana', '1011129384728', 'Gazoma Tech Ltd', 'pending', '2024-05-31 16:00:00']);

    // Invoices
    $stmtInv = $pdo->prepare("INSERT INTO invoices (merchant_id, customer_id, invoice_number, subtotal, tax, discount, total, status, due_date, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtInv->execute([$merchantId, $customerIds['Ama Serwaa'], 'INV-2024-001', 1200.00, 0.00, 0.00, 1200.00, 'paid', '2024-06-15', 'Thank you for your business.']);
    $inv1Id = $pdo->lastInsertId();
    
    $stmtInvItem = $pdo->prepare("INSERT INTO invoice_items (invoice_id, description, quantity, unit_price, amount) VALUES (?, ?, ?, ?, ?)");
    $stmtInvItem->execute([$inv1Id, 'Custom Web Software Development Services', 1, 1200.00, 1200.00]);

    $stmtInv->execute([$merchantId, $customerIds['Comfort Stores'], 'INV-2024-002', 3500.00, 175.00, 0.00, 3675.00, 'sent', '2024-06-30', 'Payment due within 30 days.']);
    $inv2Id = $pdo->lastInsertId();
    $stmtInvItem->execute([$inv2Id, 'Enterprise Payment Gateway Integration Consultancy', 1, 3500.00, 3500.00]);

    // Subscription Plans
    $stmtPlan = $pdo->prepare("INSERT INTO subscription_plans (merchant_id, name, description, amount, currency, billing_interval, trial_days, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtPlan->execute([$merchantId, 'SaaS Starter Plan', 'Basic software license plan', 150.00, 'GHS', 'monthly', 7, 'active']);
    $plan1Id = $pdo->lastInsertId();
    $stmtPlan->execute([$merchantId, 'Enterprise Growth Plan', 'Full enterprise suite access', 850.00, 'GHS', 'monthly', 14, 'active']);

    // API Keys
    $stmtApi = $pdo->prepare("INSERT INTO api_keys (merchant_id, name, key_type, public_key, secret_key_hash, secret_key_preview, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmtApi->execute([$merchantId, 'Default Live Key', 'live', 'gzm_live_pub_9a8b7c6d5e4f3a2b', password_hash('gzm_live_sec_8819203918237491', PASSWORD_BCRYPT), 'gzm_live_sec_...7491', 'active']);
    $stmtApi->execute([$merchantId, 'Testing Key', 'test', 'gzm_test_pub_1a2b3c4d5e6f7a8b', password_hash('gzm_test_sec_1122334455667788', PASSWORD_BCRYPT), 'gzm_test_sec_...7788', 'active']);

    // Webhook Endpoints
    $stmtWh = $pdo->prepare("INSERT INTO webhook_endpoints (merchant_id, url, secret, events, status) VALUES (?, ?, ?, ?, ?)");
    $stmtWh->execute([$merchantId, 'https://gazomatech.com/api/webhooks/payments', 'whsec_90192837465', json_encode(['payment.success', 'payment.failed', 'settlement.completed']), 'active']);
    $whId = $pdo->lastInsertId();

    $stmtWhLog = $pdo->prepare("INSERT INTO webhook_logs (merchant_id, endpoint_id, event_type, payload, response_code, response_body, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmtWhLog->execute([$merchantId, $whId, 'payment.success', json_encode(['event' => 'payment.success', 'data' => ['reference' => 'GZM_00012345', 'amount' => 200.00]]), 200, '{"received":true}', 'delivered']);

    // Notifications
    $stmtNotif = $pdo->prepare("INSERT INTO notifications (merchant_id, title, message, type, is_read) VALUES (?, ?, ?, ?, ?)");
    $stmtNotif->execute([$merchantId, 'Payment Received', 'Ama Serwaa paid GH₵ 200.00 via Card.', 'success', 0]);
    $stmtNotif->execute([$merchantId, 'Settlement Processing', 'Settlement SET_89230194 for GH₵ 4,250.00 is now pending approval.', 'info', 0]);
    $stmtNotif->execute([$merchantId, 'New Payment Link Created', 'Payment link "iPhone 15 Payment" was accessed 156 times.', 'info', 1]);

    echo "=== Gazoma Pay Database Seeded Successfully! ===\n";

} catch (Exception $e) {
    die("Seeding Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
}
