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
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Super Admin Platform Console</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Full-stack platform control, KYB verification approvals, custom merchant fee tiers, balance adjustments, and dispute overrides.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="px-3.5 py-1.5 bg-emerald-50 text-emerald-700 font-data-mono text-xs font-bold rounded-xl border border-emerald-200 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Platform Core Operational</span>
        </div>
    </div>
</div>

<!-- 5 Bento Metric Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <!-- 1. Total System Volume -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36 border border-outline-variant">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Total Platform Volume</span>
            <div class="w-8 h-8 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">payments</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-xl text-on-surface mb-1 font-bold font-data-mono">GH₵ <?= number_format($totalSystemVolume ?: 126560.00, 2) ?></div>
            <div class="flex items-center gap-1 font-body-sm text-xs text-[#10B981]">
                <span class="material-symbols-outlined text-[14px]">trending_up</span>
                <span>Active Volume</span>
            </div>
        </div>
    </div>

    <!-- 2. Platform Revenue -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36 border border-outline-variant">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Fee Earnings</span>
            <div class="w-8 h-8 rounded-full bg-[#10B981]/10 text-[#10B981] flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">monetization_on</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-xl text-[#10B981] mb-1 font-bold font-data-mono">GH₵ <?= number_format($totalPlatformRevenue ?: 1898.40, 2) ?></div>
            <div class="font-body-sm text-[11px] text-on-surface-variant">Fee Cuts</div>
        </div>
    </div>

    <!-- 3. Registered Merchants -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36 border border-outline-variant">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Merchants</span>
            <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">store</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-xl text-on-surface mb-1 font-bold font-data-mono"><?= $totalMerchants ?> Businesses</div>
            <div class="font-body-sm text-[11px] text-on-surface-variant">Multi-tenant</div>
        </div>
    </div>

    <!-- 4. Pending Payouts -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36 border border-outline-variant">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Pending Payouts</span>
            <div class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-xl text-amber-600 mb-1 font-bold font-data-mono"><?= $pendingPayoutCount ?> Pending</div>
            <div class="font-body-sm text-[11px] text-amber-700 font-bold">Clearance Queue</div>
        </div>
    </div>

    <!-- 5. Active Disputes -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36 border border-outline-variant">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Disputes</span>
            <div class="w-8 h-8 rounded-full bg-rose-500/10 text-rose-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">gavel</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-xl text-rose-600 mb-1 font-bold font-data-mono"><?= $activeDisputesCount ?> Open</div>
            <div class="font-body-sm text-[11px] text-rose-700 font-bold">Chargebacks</div>
        </div>
    </div>
</div>

<!-- Global Platform Settings Control Card -->
<div class="glass-card rounded-xl p-6 border border-outline-variant mb-8 bg-surface">
    <div class="flex justify-between items-center mb-4 pb-3 border-b border-outline-variant">
        <div>
            <h3 class="font-headline-md text-base text-on-surface font-bold">Global Gateway & Fee Engine Configuration</h3>
            <p class="font-body-sm text-xs text-on-surface-variant">Update default processing fee rates and manage global maintenance mode.</p>
        </div>
        <span class="px-2.5 py-1 rounded bg-surface-container-high text-on-surface font-data-mono text-xs font-bold"><?= htmlspecialchars($platformSettings['gateway_driver'] ?? 'Sandbox Payment Gateway') ?></span>
    </div>

    <form action="/admin/settings/update" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">

        <div>
            <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Default Fee Rate (%)</label>
            <input type="number" step="0.01" name="platform_fee_percent" value="<?= htmlspecialchars($platformSettings['platform_fee_percent'] ?? '1.50') ?>" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg font-body-sm font-data-mono text-sm" required>
        </div>

        <div>
            <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Default Flat Fee (GHS)</label>
            <input type="number" step="0.01" name="platform_fee_flat" value="<?= htmlspecialchars($platformSettings['platform_fee_flat'] ?? '0.50') ?>" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg font-body-sm font-data-mono text-sm" required>
        </div>

        <div>
            <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Maintenance Mode</label>
            <select name="maintenance_mode" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg font-body-sm text-sm cursor-pointer">
                <option value="0" <?= ($platformSettings['maintenance_mode'] ?? '0') === '0' ? 'selected' : '' ?>>🟢 Live & Accepting Payments</option>
                <option value="1" <?= ($platformSettings['maintenance_mode'] ?? '0') === '1' ? 'selected' : '' ?>>🔴 Maintenance Mode (Paused)</option>
            </select>
        </div>

        <div>
            <button type="submit" class="w-full px-4 py-2.5 bg-primary text-on-primary font-body-sm text-xs font-bold rounded-lg hover:bg-primary/90 transition-colors shadow-sm cursor-pointer flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[16px]">save</span>
                <span>Save Platform Settings</span>
            </button>
        </div>
    </form>
</div>

<!-- Section 1: Merchant Directory & KYB Verification Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden mb-8">
    <div class="p-6 border-b border-outline-variant bg-surface flex justify-between items-center">
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-semibold">Platform Merchants, Custom Rates & KYB Directory</h3>
            <p class="font-body-sm text-xs text-on-surface-variant">Approve business KYC/KYB documents, suspend accounts, configure custom fee tiers, and adjust merchant balances.</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-primary/10 text-primary font-body-sm text-xs font-bold"><?= count($merchants) ?> Merchants</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase">
                    <th class="py-3.5 px-6">Merchant Code</th>
                    <th class="py-3.5 px-6">Business Name</th>
                    <th class="py-3.5 px-6">Contact Email</th>
                    <th class="py-3.5 px-6">Fee Structure</th>
                    <th class="py-3.5 px-6">Available Balance</th>
                    <th class="py-3.5 px-6">KYC / KYB</th>
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
                        <td class="py-4 px-6 font-data-mono text-xs text-on-surface-variant">
                            <?php if (!empty($m['custom_fee_percentage'])): ?>
                                <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-bold border border-indigo-200"><?= number_format($m['custom_fee_percentage'], 2) ?>% + GH₵ <?= number_format($m['custom_fee_flat'] ?? 0.50, 2) ?></span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded bg-surface-container-high text-on-surface-variant">Default (1.50% + 0.50)</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-6 font-data-mono font-bold text-[#10B981]">GH₵ <?= number_format($m['available_balance'], 2) ?></td>
                        <td class="py-4 px-6"><?= Format::statusBadge($m['kyc_status'] ?? 'approved') ?></td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <?php if (($m['kyc_status'] ?? 'approved') !== 'approved'): ?>
                                    <form method="POST" action="/admin/merchants/<?= $m['id'] ?>/approve-kyc">
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 text-white font-body-sm text-xs font-semibold rounded hover:bg-emerald-500 transition-colors flex items-center gap-1 cursor-pointer">
                                            <span class="material-symbols-outlined text-[14px]">verified</span>
                                            <span>Approve</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="/admin/merchants/<?= $m['id'] ?>/reject-kyc">
                                        <button type="submit" class="px-2 py-1 bg-rose-50 text-rose-700 font-body-sm text-xs font-semibold rounded border border-rose-200 hover:bg-rose-100 transition-colors cursor-pointer">Reject</button>
                                    </form>
                                <?php endif; ?>

                                <button type="button" class="px-2.5 py-1 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-body-sm text-xs font-semibold rounded border border-outline-variant transition-colors flex items-center gap-1 cursor-pointer" onclick="openBalanceModal(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['name'])) ?>', <?= $m['available_balance'] ?>)">
                                    <span class="material-symbols-outlined text-[14px]">account_balance</span>
                                    <span>Adjust Bal</span>
                                </button>

                                <button type="button" class="px-2.5 py-1 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-body-sm text-xs font-semibold rounded border border-outline-variant transition-colors flex items-center gap-1 cursor-pointer" onclick="openFeeModal(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['name'])) ?>', '<?= $m['custom_fee_percentage'] ?? '' ?>', '<?= $m['custom_fee_flat'] ?? '' ?>')">
                                    <span class="material-symbols-outlined text-[14px]">percent</span>
                                    <span>Fee Rate</span>
                                </button>

                                <form method="POST" action="/admin/merchants/<?= $m['id'] ?>/toggle-status">
                                    <button type="submit" class="px-2.5 py-1 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-body-sm text-xs font-semibold rounded border border-outline-variant transition-colors flex items-center gap-1 cursor-pointer" title="<?= ($m['account_status'] ?? 'active') === 'active' ? 'Suspend Account' : 'Activate Account' ?>">
                                        <span class="material-symbols-outlined text-[14px]"><?= ($m['account_status'] ?? 'active') === 'active' ? 'block' : 'check_circle' ?></span>
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

<!-- Section 3: Global System-Wide Disputes & Chargebacks Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden mb-8">
    <div class="p-6 border-b border-outline-variant bg-surface flex justify-between items-center">
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-semibold">Global Disputes & Chargeback Resolution</h3>
            <p class="font-body-sm text-xs text-on-surface-variant">System-wide customer disputes requiring superadmin override resolution.</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-700 font-body-sm text-xs font-bold"><?= count($disputes) ?> System Disputes</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase">
                    <th class="py-3.5 px-6">Dispute Ref</th>
                    <th class="py-3.5 px-6">Merchant</th>
                    <th class="py-3.5 px-6">Tx Ref</th>
                    <th class="py-3.5 px-6">Amount</th>
                    <th class="py-3.5 px-6">Reason</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6 text-center">Superadmin Resolution</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                <?php if (empty($disputes)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-10 text-on-surface-variant">No active disputes reported.</td>
                    </tr>
                <?php else: foreach ($disputes as $d): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="py-4 px-6 font-data-mono font-bold text-rose-600"><?= htmlspecialchars($d['dispute_code']) ?></td>
                        <td class="py-4 px-6 font-semibold text-on-surface"><?= htmlspecialchars($d['merchant_name']) ?></td>
                        <td class="py-4 px-6 font-data-mono text-xs text-secondary"><?= htmlspecialchars($d['tx_reference']) ?></td>
                        <td class="py-4 px-6 font-data-mono font-bold text-on-surface">GH₵ <?= number_format($d['amount'], 2) ?></td>
                        <td class="py-4 px-6 text-on-surface-variant capitalize"><?= str_replace('_', ' ', $d['reason']) ?></td>
                        <td class="py-4 px-6"><?= Format::statusBadge($d['status']) ?></td>
                        <td class="py-4 px-6 text-center">
                            <?php if ($d['status'] !== 'won' && $d['status'] !== 'lost'): ?>
                                <div class="flex items-center justify-center gap-2">
                                    <form method="POST" action="/admin/disputes/<?= $d['id'] ?>/resolve">
                                        <input type="hidden" name="status" value="won">
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-body-sm text-xs font-semibold rounded border border-emerald-200 hover:bg-emerald-100 transition-colors cursor-pointer">Rule Won</button>
                                    </form>
                                    <form method="POST" action="/admin/disputes/<?= $d['id'] ?>/resolve">
                                        <input type="hidden" name="status" value="lost">
                                        <button type="submit" class="px-2.5 py-1 bg-rose-50 text-rose-700 font-body-sm text-xs font-semibold rounded border border-rose-200 hover:bg-rose-100 transition-colors cursor-pointer">Rule Lost</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="text-xs text-on-surface-variant font-data-mono font-bold">Resolved</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Section 4: Global System Audit Trail Logs Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant bg-surface flex justify-between items-center">
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-semibold">Global Immutable System Audit Trail</h3>
            <p class="font-body-sm text-xs text-on-surface-variant">Security logging for platform actions, balance adjustments, and operator clearances.</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-secondary/10 text-secondary font-body-sm text-xs font-bold">Live System Log</span>
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

<!-- Modal 1: Adjust Merchant Balance Modal -->
<div class="modal-overlay hidden fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" id="adjustBalanceModal">
    <div class="bg-surface border border-outline-variant rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-outline-variant pb-3">
            <h3 class="font-headline-lg text-lg font-bold text-on-surface">Adjust Merchant Balance</h3>
            <button class="text-on-surface-variant hover:text-on-surface text-xl cursor-pointer" onclick="closeModal('adjustBalanceModal')">&times;</button>
        </div>
        <form id="adjustBalanceForm" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Target Merchant</label>
                <input type="text" id="balModalMerchantName" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface font-bold" readonly>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Adjustment Type *</label>
                    <select name="adjustment_type" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary cursor-pointer">
                        <option value="credit">➕ Credit (Add Funds)</option>
                        <option value="debit">➖ Debit (Deduct Funds)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Amount (GHS) *</label>
                    <input type="number" step="0.01" name="amount" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm font-data-mono text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="500.00" required>
                </div>
            </div>

            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Audit Log Reason *</label>
                <input type="text" name="reason" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="Manual payout correction / promotional credit" required>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-outline-variant">
                <button type="button" class="px-4 py-2 bg-surface-container-high text-on-surface font-body-sm text-xs font-bold rounded-xl hover:bg-surface-container-highest" onclick="closeModal('adjustBalanceModal')">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm text-xs font-bold rounded-xl hover:bg-primary/90 shadow-sm cursor-pointer">Confirm Adjustment</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Configure Custom Merchant Fee Rate Modal -->
<div class="modal-overlay hidden fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" id="configureFeeModal">
    <div class="bg-surface border border-outline-variant rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-outline-variant pb-3">
            <h3 class="font-headline-lg text-lg font-bold text-on-surface">Configure Custom Merchant Fee Rate</h3>
            <button class="text-on-surface-variant hover:text-on-surface text-xl cursor-pointer" onclick="closeModal('configureFeeModal')">&times;</button>
        </div>
        <form id="configureFeeForm" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Target Merchant</label>
                <input type="text" id="feeModalMerchantName" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface font-bold" readonly>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Custom Fee Rate (%)</label>
                    <input type="number" step="0.01" name="custom_fee_percentage" id="feeModalPct" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm font-data-mono text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="1.20">
                </div>
                <div>
                    <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Custom Flat Fee (GHS)</label>
                    <input type="number" step="0.01" name="custom_fee_flat" id="feeModalFlat" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm font-data-mono text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="0.30">
                </div>
            </div>
            <p class="font-body-sm text-[11px] text-on-surface-variant">Leave blank to revert merchant to global platform default (1.50% + GH₵ 0.50).</p>

            <div class="flex justify-end gap-3 pt-3 border-t border-outline-variant">
                <button type="button" class="px-4 py-2 bg-surface-container-high text-on-surface font-body-sm text-xs font-bold rounded-xl hover:bg-surface-container-highest" onclick="closeModal('configureFeeModal')">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm text-xs font-bold rounded-xl hover:bg-primary/90 shadow-sm cursor-pointer">Save Fee Structure</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    const m = document.getElementById(id);
    if (m) {
        m.classList.remove('hidden');
        m.classList.add('open');
    }
}
function closeModal(id) {
    const m = document.getElementById(id);
    if (m) {
        m.classList.remove('open');
        m.classList.add('hidden');
    }
}

function openBalanceModal(mId, mName, currBal) {
    document.getElementById('balModalMerchantName').value = mName + ' (Curr: GH₵ ' + parseFloat(currBal).toFixed(2) + ')';
    document.getElementById('adjustBalanceForm').action = '/admin/merchants/' + mId + '/adjust-balance';
    openModal('adjustBalanceModal');
}

function openFeeModal(mId, mName, pct, flat) {
    document.getElementById('feeModalMerchantName').value = mName;
    document.getElementById('feeModalPct').value = pct || '';
    document.getElementById('feeModalFlat').value = flat || '';
    document.getElementById('configureFeeForm').action = '/admin/merchants/' + mId + '/update-fee';
    openModal('configureFeeModal');
}
</script>
