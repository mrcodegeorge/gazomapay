<?php if (empty($merchant['onboarding_completed'])): ?>
    <div class="p-5 mb-8 bg-gradient-to-r from-blue-900/30 via-slate-900 to-indigo-900/30 border border-blue-500/30 rounded-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-xl">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center font-bold text-xl shrink-0">
                <span class="material-symbols-outlined text-[24px]">verified</span>
            </div>
            <div>
                <h4 class="font-headline-md text-base font-bold text-white mb-0.5">Complete Merchant Onboarding & KYB Verification</h4>
                <p class="font-body-sm text-xs text-slate-300">Set up your registered business details, payout bank account, and director identification to activate live processing.</p>
            </div>
        </div>
        <a href="/onboarding" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-body-sm text-xs font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2 shrink-0">
            <span>Continue Setup</span>
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
        </a>
    </div>
<?php endif; ?>

<!-- Top Action Toolbar -->
<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Financial Overview</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Real-time merchant transactions, double-entry ledger balance, and revenue performance.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="/analytics/report-csv" class="px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-sm text-body-sm font-semibold text-on-surface hover:bg-surface-container-low transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">download</span>
            Download CSV Report
        </a>
    </div>
</div>

<!-- 4 Bento Grid Metric Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Volume -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Total Gross Volume</span>
            <div class="w-8 h-8 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">payments</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg text-on-surface mb-1 font-semibold">GH₵ <?= number_format($totalVolume, 2) ?></div>
            <div class="flex items-center gap-1 font-body-sm text-body-sm text-[#10B981]">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>+12.5% vs last month</span>
            </div>
        </div>
    </div>

    <!-- Successful Charges -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Successful Charges</span>
            <div class="w-8 h-8 rounded-full bg-[#10B981]/10 text-[#10B981] flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg text-on-surface mb-1 font-semibold"><?= number_format($successfulTxCount) ?></div>
            <div class="flex items-center gap-1 font-body-sm text-body-sm text-[#10B981]">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>+8.7% vs last month</span>
            </div>
        </div>
    </div>

    <!-- Active Customers -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Total Customers</span>
            <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">group</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg text-on-surface mb-1 font-semibold"><?= number_format($totalCustomers) ?></div>
            <div class="flex items-center gap-1 font-body-sm text-body-sm text-[#10B981]">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>+10.3% vs last month</span>
            </div>
        </div>
    </div>

    <!-- Ledger Available Balance -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36 border-l-4 border-l-secondary">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Available Balance</span>
            <div class="w-8 h-8 rounded-lg bg-surface-variant text-on-surface-variant flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
            </div>
        </div>
        <div class="flex items-end justify-between">
            <div>
                <div class="font-headline-lg text-headline-lg text-on-surface font-semibold">GH₵ <?= number_format($availableBalance, 2) ?></div>
                <span class="font-body-sm text-body-sm text-on-surface-variant">Ledger Verified</span>
            </div>
            <button onclick="openModal('withdrawModal')" class="px-3 py-1.5 bg-secondary text-on-secondary font-body-sm text-xs font-semibold rounded-md hover:bg-secondary-container transition-colors cursor-pointer">
                Withdraw
            </button>
        </div>
    </div>
</div>

<!-- Main Dashboard Grid: Analytics Chart & Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Interactive Line Chart (2 Columns) -->
    <div class="lg:col-span-2 glass-card rounded-xl p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-headline-md text-headline-md text-on-surface font-semibold">Revenue Analytics</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Gross volume transactions overview</p>
            </div>
            <select class="px-3 py-1.5 bg-surface-container-low border border-outline-variant rounded-lg font-body-sm text-body-sm text-on-surface focus:outline-none" onchange="showToast('Chart range updated')">
                <option value="This Month">This Month</option>
                <option value="Today">Today</option>
                <option value="7 Days">7 Days</option>
                <option value="30 Days">30 Days</option>
            </select>
        </div>
        <div class="h-72 w-full relative">
            <canvas id="overviewChart"></canvas>
        </div>
    </div>

    <!-- Recent Transactions List Card -->
    <div class="glass-card rounded-xl p-6 flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-headline-md text-headline-md text-on-surface font-semibold">Recent Activity</h3>
            <a href="/transactions" class="font-label-caps text-label-caps text-secondary hover:underline">View All</a>
        </div>
        <div class="space-y-4 flex-1 overflow-y-auto">
            <?php foreach (array_slice($recentTransactions, 0, 5) as $tx): ?>
                <div class="p-3 bg-surface-container-low/60 hover:bg-surface-container-low rounded-lg transition-colors flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-surface-variant flex items-center justify-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-[18px]">person</span>
                        </div>
                        <div>
                            <div class="font-body-sm text-body-sm font-medium text-on-surface"><?= htmlspecialchars($tx['customer_name'] ?? 'Guest Customer') ?></div>
                            <div class="font-label-caps text-[11px] text-on-surface-variant"><?= Format::dateShort($tx['created_at']) ?></div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-data-mono font-semibold text-on-surface text-body-sm">GH₵ <?= number_format($tx['amount'], 2) ?></div>
                        <div class="mt-0.5"><?= Format::statusBadge($tx['status']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal-overlay" id="withdrawModal">
    <div class="modal-card max-w-[480px]">
        <div class="modal-header">
            <h3 class="modal-title">Request Settlement Payout</h3>
            <button class="modal-close" onclick="closeModal('withdrawModal')">&times;</button>
        </div>
        <form action="/settlements/request" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group mb-4">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Available Balance</label>
                <input type="text" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface-container font-data-mono font-bold text-on-surface" value="GH₵ <?= number_format($availableBalance, 2) ?>" readonly>
            </div>
            <div class="form-group mb-4">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Withdrawal Amount (GHS)</label>
                <input type="number" step="0.01" max="<?= $availableBalance ?>" name="amount" class="w-full px-3 py-2 border border-outline-variant rounded font-body-md" value="<?= $availableBalance ?>" required>
            </div>
            <div class="form-group mb-6">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Destination Bank Account</label>
                <select name="bank_name" class="w-full px-3 py-2 border border-outline-variant rounded bg-white font-body-sm">
                    <option value="GCB Bank Ghana - 1011129384728">GCB Bank Ghana (1011129384728)</option>
                    <option value="Stanbic Bank Ghana - 9040001827364">Stanbic Bank Ghana (9040001827364)</option>
                    <option value="Ecobank Ghana - 4401928374651">Ecobank Ghana (4401928374651)</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" class="px-4 py-2 border border-outline-variant rounded font-body-sm text-on-surface hover:bg-surface-container-low" onclick="closeModal('withdrawModal')">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm font-medium rounded hover:bg-primary/90 cursor-pointer">Confirm Withdrawal</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    initRevenueChart('overviewChart', <?= $chartLabels ?>, <?= $chartData ?>);
});
</script>
