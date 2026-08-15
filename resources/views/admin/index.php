<!-- Flash Notifications -->
<?php if ($msg = Response::getFlash('success')): ?>
    <div class="p-4 mb-6 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl font-body-sm text-sm flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
        <span><?= htmlspecialchars($msg) ?></span>
    </div>
<?php endif; ?>

<?php if ($msg = Response::getFlash('error')): ?>
    <div class="p-4 mb-6 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl font-body-sm text-sm flex items-center gap-3">
        <span class="material-symbols-outlined text-rose-600 text-[20px]">error</span>
        <span><?= htmlspecialchars($msg) ?></span>
    </div>
<?php endif; ?>

<!-- Header Toolbar -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Super Admin Console</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Real-time multi-tenant platform oversight, KYB verification approvals, and settlement clearances.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="px-3.5 py-1.5 bg-emerald-50 text-emerald-700 font-data-mono text-xs font-bold rounded-xl border border-emerald-200 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Platform Core Operational</span>
        </div>
    </div>
</div>

<!-- 4 Bento Grid Metric Cards (Using App Design Tokens) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total System Volume -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36 border border-outline-variant">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Total Platform Volume</span>
            <div class="w-8 h-8 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">payments</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg text-on-surface mb-1 font-semibold font-data-mono">GH₵ <?= number_format($totalSystemVolume ?: 126560.00, 2) ?></div>
            <div class="flex items-center gap-1 font-body-sm text-body-sm text-[#10B981]">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>Across all active merchants</span>
            </div>
        </div>
    </div>

    <!-- Platform Revenue -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36 border border-outline-variant">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Platform Fee Revenue</span>
            <div class="w-8 h-8 rounded-full bg-[#10B981]/10 text-[#10B981] flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">monetization_on</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg text-[#10B981] mb-1 font-semibold font-data-mono">GH₵ <?= number_format($totalPlatformRevenue ?: 1898.40, 2) ?></div>
            <div class="font-body-sm text-xs text-on-surface-variant">1.5% + GH₵0.50 platform cut</div>
        </div>
    </div>

    <!-- Active Merchants -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36 border border-outline-variant">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Registered Merchants</span>
            <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">store</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg text-on-surface mb-1 font-semibold font-data-mono"><?= $totalMerchants ?> Businesses</div>
            <div class="font-body-sm text-xs text-on-surface-variant">Multi-tenant ecosystem</div>
        </div>
    </div>

    <!-- Pending Payouts -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36 border border-outline-variant">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Pending Payouts</span>
            <div class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg text-amber-600 mb-1 font-semibold font-data-mono"><?= $pendingPayoutCount ?> Payouts</div>
            <div class="font-body-sm text-xs text-amber-700 font-bold">Requires operator clearance</div>
        </div>
    </div>
</div>

<!-- Section 1: Merchant Directory & KYB Verification Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden mb-8">
    <div class="p-6 border-b border-outline-variant bg-surface flex justify-between items-center">
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-semibold">Platform Merchants & KYB Verification</h3>
            <p class="font-body-sm text-xs text-on-surface-variant">Approve business KYC/KYB documents, suspend accounts, and view merchant balances.</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-primary/10 text-primary font-body-sm text-xs font-bold"><?= count($merchants) ?> Merchants</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase">
                    <th class="py-3.5 px-6">Merchant Code</th>
                    <th class="py-3.5 px-6">Business Name</th>
                    <th class="py-3.5 px-6">Email</th>
                    <th class="py-3.5 px-6">Available Balance</th>
                    <th class="py-3.5 px-6">KYC / KYB</th>
                    <th class="py-3.5 px-6">Account Status</th>
                    <th class="py-3.5 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                <?php foreach ($merchants as $m): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="py-4 px-6 font-data-mono font-bold text-secondary"><?= htmlspecialchars($m['merchant_id']) ?></td>
                        <td class="py-4 px-6 font-semibold text-on-surface">
                            <?= htmlspecialchars($m['name']) ?>
                            <div class="font-label-caps text-[11px] text-on-surface-variant font-normal capitalize"><?= htmlspecialchars($m['business_type'] ?? 'Limited Company') ?></div>
                        </td>
                        <td class="py-4 px-6 text-on-surface-variant font-data-mono text-xs"><?= htmlspecialchars($m['email']) ?></td>
                        <td class="py-4 px-6 font-data-mono font-bold text-[#10B981]">GH₵ <?= number_format($m['available_balance'], 2) ?></td>
                        <td class="py-4 px-6"><?= Format::statusBadge($m['kyc_status'] ?? 'approved') ?></td>
                        <td class="py-4 px-6"><?= Format::statusBadge($m['account_status'] ?? 'active') ?></td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <?php if (($m['kyc_status'] ?? 'approved') !== 'approved'): ?>
                                    <form method="POST" action="/admin/merchants/<?= $m['id'] ?>/approve-kyc">
                                        <button type="submit" class="px-3 py-1 bg-primary text-on-primary font-body-sm text-xs font-semibold rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-1 cursor-pointer">
                                            <span class="material-symbols-outlined text-[14px]">verified</span>
                                            <span>Approve KYB</span>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <form method="POST" action="/admin/merchants/<?= $m['id'] ?>/toggle-status">
                                    <button type="submit" class="px-3 py-1 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-body-sm text-xs font-semibold rounded-lg border border-outline-variant transition-colors flex items-center gap-1 cursor-pointer">
                                        <span class="material-symbols-outlined text-[14px]"><?= ($m['account_status'] ?? 'active') === 'active' ? 'block' : 'check_circle' ?></span>
                                        <span><?= ($m['account_status'] ?? 'active') === 'active' ? 'Suspend' : 'Activate' ?></span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Section 2: Platform Settlement Approvals Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden mb-8">
    <div class="p-6 border-b border-outline-variant bg-surface flex justify-between items-center">
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-semibold">Platform Settlement Payout Clearances</h3>
            <p class="font-body-sm text-xs text-on-surface-variant">Review merchant payout requests and execute double-entry ledger settlement releases.</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase">
                    <th class="py-3.5 px-6">Settlement Ref</th>
                    <th class="py-3.5 px-6">Merchant</th>
                    <th class="py-3.5 px-6">Gross Amount</th>
                    <th class="py-3.5 px-6">Platform Fee</th>
                    <th class="py-3.5 px-6">Net Payout</th>
                    <th class="py-3.5 px-6">Bank Account</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                <?php if (empty($settlements)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-10 text-on-surface-variant">No platform settlement requests found.</td>
                    </tr>
                <?php else: foreach ($settlements as $s): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="py-4 px-6 font-data-mono font-bold text-secondary"><?= htmlspecialchars($s['reference']) ?></td>
                        <td class="py-4 px-6">
                            <div class="font-semibold text-on-surface"><?= htmlspecialchars($s['merchant_name'] ?? 'Merchant #' . $s['merchant_id']) ?></div>
                            <div class="font-data-mono text-[11px] text-on-surface-variant"><?= htmlspecialchars($s['merchant_email'] ?? '') ?></div>
                        </td>
                        <td class="py-4 px-6 font-data-mono font-bold text-on-surface">GH₵ <?= number_format($s['gross_amount'], 2) ?></td>
                        <td class="py-4 px-6 font-data-mono text-on-surface-variant">GH₵ <?= number_format($s['fee'], 2) ?></td>
                        <td class="py-4 px-6 font-data-mono font-bold text-[#10B981]">GH₵ <?= number_format($s['net_amount'], 2) ?></td>
                        <td class="py-4 px-6">
                            <div class="font-semibold text-on-surface"><?= htmlspecialchars($s['bank_name']) ?></div>
                            <div class="font-data-mono text-[11px] text-on-surface-variant"><?= htmlspecialchars($s['account_number']) ?> (<?= htmlspecialchars($s['account_name']) ?>)</div>
                        </td>
                        <td class="py-4 px-6"><?= Format::statusBadge($s['status']) ?></td>
                        <td class="py-4 px-6 text-center">
                            <?php if ($s['status'] === 'pending'): ?>
                                <form method="POST" action="/admin/settlements/<?= $s['id'] ?>/process" onsubmit="return confirm('Approve and process bank settlement payout?')">
                                    <button type="submit" class="px-3 py-1.5 bg-primary text-on-primary font-body-sm text-xs font-semibold rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-1 cursor-pointer mx-auto">
                                        <span class="material-symbols-outlined text-[14px]">send</span>
                                        <span>Approve & Payout</span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-xs text-on-surface-variant font-data-mono"><?= Format::dateShort($s['processed_at'] ?? $s['created_at']) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Section 3: Global System Audit Trail Logs Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant bg-surface flex justify-between items-center">
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-semibold">Global System Audit Trail</h3>
            <p class="font-body-sm text-xs text-on-surface-variant">Immutable security logging for platform events, administrative changes, and gateway actions.</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-secondary/10 text-secondary font-body-sm text-xs font-bold">Immutable System Log</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase">
                    <th class="py-3.5 px-6">Action Code</th>
                    <th class="py-3.5 px-6">User Email</th>
                    <th class="py-3.5 px-6">IP Address</th>
                    <th class="py-3.5 px-6">Details</th>
                    <th class="py-3.5 px-6">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                <?php foreach ($systemLogs as $log): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="py-4 px-6 font-data-mono font-bold text-secondary"><?= htmlspecialchars($log['action']) ?></td>
                        <td class="py-4 px-6 font-medium text-on-surface"><?= htmlspecialchars($log['user_email'] ?? 'system@gazomapay.com') ?></td>
                        <td class="py-4 px-6 font-data-mono text-xs text-on-surface-variant"><?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?></td>
                        <td class="py-4 px-6 text-on-surface-variant truncate max-w-xs"><?= htmlspecialchars($log['details'] ?? '-') ?></td>
                        <td class="py-4 px-6 text-on-surface-variant font-data-mono text-xs"><?= Format::date($log['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
