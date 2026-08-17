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

<!-- Top Page Banner & Action Header -->
<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="font-display-md text-display-md text-on-surface">Good Morning, <span class="font-bold text-primary">Super Admin</span></h2>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Here is what's happening with Gazomapay today.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="flex bg-surface-container-lowest rounded-lg border border-outline-variant p-1">
            <button class="px-4 py-1.5 rounded-md bg-surface-container-low font-label-bold text-label-bold text-on-surface shadow-sm text-xs">Overview</button>
            <button class="px-4 py-1.5 rounded-md font-label-bold text-label-bold text-on-surface-variant hover:bg-surface-container-low/50 transition-colors text-xs">Audiences</button>
            <button class="px-4 py-1.5 rounded-md font-label-bold text-label-bold text-on-surface-variant hover:bg-surface-container-low/50 transition-colors text-xs">Demographics</button>
        </div>
        <button onclick="window.print()" class="h-10 px-4 bg-primary text-on-primary rounded-lg font-label-bold text-label-bold flex items-center gap-2 hover:bg-primary/90 transition-colors cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">download</span>
            Export Report
        </button>
    </div>
</div>

<!-- 4 Top Stat Cards (Star Admin Fintech Styling) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stat Card 1: Total Revenue -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm flex flex-col justify-between h-full">
        <div class="flex justify-between items-start mb-4">
            <p class="font-body-sm text-body-sm text-on-surface-variant uppercase tracking-wider font-semibold">Total Revenue (Fees)</p>
            <div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-[20px]">account_balance</span>
            </div>
        </div>
        <div>
            <h3 class="font-stat-value text-stat-value text-on-surface font-data-mono">GH₵ <?= number_format($totalPlatformRevenue ?: 1898.40, 2) ?></h3>
            <div class="flex items-center gap-1 mt-2 text-[#059669]">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span class="font-label-bold text-label-bold text-xs">+12% from last month</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 2: Active Merchants -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm flex flex-col justify-between h-full">
        <div class="flex justify-between items-start mb-4">
            <p class="font-body-sm text-body-sm text-on-surface-variant uppercase tracking-wider font-semibold">Active Merchants</p>
            <div class="w-8 h-8 rounded bg-secondary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-secondary text-[20px]">store</span>
            </div>
        </div>
        <div>
            <h3 class="font-stat-value text-stat-value text-on-surface font-data-mono"><?= number_format($totalMerchants) ?></h3>
            <div class="flex items-center gap-1 mt-2 text-[#059669]">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span class="font-label-bold text-label-bold text-xs">+5% from last month</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 3: Success Rate -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm flex flex-col justify-between h-full relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary/5 rounded-full blur-xl group-hover:bg-primary/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <p class="font-body-sm text-body-sm text-on-surface-variant uppercase tracking-wider font-semibold">Success Rate</p>
            <div class="w-8 h-8 rounded bg-emerald-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
            </div>
        </div>
        <div class="relative z-10">
            <h3 class="font-stat-value text-stat-value text-on-surface font-data-mono">99.8%</h3>
            <div class="w-full bg-surface-container-high h-1.5 rounded-full mt-3 overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full" style="width: 99.8%"></div>
            </div>
        </div>
    </div>

    <!-- Stat Card 4: Pending Settlements -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm flex flex-col justify-between h-full">
        <div class="flex justify-between items-start mb-4">
            <p class="font-body-sm text-body-sm text-on-surface-variant uppercase tracking-wider font-semibold">Pending Settlements</p>
            <div class="w-8 h-8 rounded bg-amber-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-600 text-[20px]">pending_actions</span>
            </div>
        </div>
        <div>
            <h3 class="font-stat-value text-stat-value text-on-surface font-data-mono"><?= $pendingPayoutCount ?></h3>
            <div class="flex items-center gap-1 mt-2 text-amber-700">
                <span class="material-symbols-outlined text-[16px]">schedule</span>
                <span class="font-body-sm text-body-sm text-xs font-bold">Requires attention today</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Charts & Daily Target Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Large Line Chart: Transaction Volume -->
    <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm flex flex-col">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center">
            <div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Transaction Volume</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Total System Volume: GH₵ <?= number_format($totalSystemVolume ?: 126560.00, 2) ?></p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-primary"></div>
                    <span class="font-body-sm text-body-sm text-on-surface-variant text-xs font-semibold">This Week</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-sky-400"></div>
                    <span class="font-body-sm text-body-sm text-on-surface-variant text-xs font-semibold">Last Week</span>
                </div>
            </div>
        </div>
        <div class="p-6 flex-1 min-h-[300px] relative w-full">
            <div class="absolute inset-0 p-6 flex items-end">
                <!-- Y Axis labels -->
                <div class="absolute left-6 top-6 bottom-6 flex flex-col justify-between text-xs text-on-surface-variant font-data-mono w-8">
                    <span>40k</span>
                    <span>30k</span>
                    <span>20k</span>
                    <span>10k</span>
                    <span>0</span>
                </div>
                <!-- Grid lines -->
                <div class="absolute left-16 right-6 top-6 bottom-8 flex flex-col justify-between pointer-events-none">
                    <div class="w-full h-px bg-outline-variant/30"></div>
                    <div class="w-full h-px bg-outline-variant/30"></div>
                    <div class="w-full h-px bg-outline-variant/30"></div>
                    <div class="w-full h-px bg-outline-variant/30"></div>
                    <div class="w-full h-px bg-outline-variant/30"></div>
                </div>
                <!-- X Axis labels -->
                <div class="absolute left-16 right-6 bottom-2 flex justify-between text-xs text-on-surface-variant font-body-sm px-4">
                    <span>SUN</span>
                    <span>MON</span>
                    <span>TUE</span>
                    <span>WED</span>
                    <span>THU</span>
                    <span>FRI</span>
                    <span>SAT</span>
                </div>
                <!-- SVG Vector Chart Lines -->
                <div class="absolute left-16 right-6 top-6 bottom-8 pointer-events-none overflow-hidden">
                    <svg class="w-full h-full absolute inset-0" preserveAspectRatio="none" viewBox="0 0 100 100">
                        <path d="M0,80 Q15,75 25,70 T50,30 T75,60 T100,50 L100,100 L0,100 Z" fill="url(#blue-gradient-admin)" opacity="0.15"></path>
                        <path d="M0,80 Q15,75 25,70 T50,30 T75,60 T100,50" fill="none" stroke="#002293" stroke-width="2.5" vector-effect="non-scaling-stroke"></path>
                        <circle cx="0" cy="80" fill="#002293" r="2"></circle>
                        <circle cx="25" cy="70" fill="#002293" r="2"></circle>
                        <circle cx="50" cy="30" fill="#002293" r="2"></circle>
                        <circle cx="75" cy="60" fill="#002293" r="2"></circle>
                        <circle cx="100" cy="50" fill="#002293" r="2"></circle>
                    </svg>
                    <svg class="w-full h-full absolute inset-0" preserveAspectRatio="none" viewBox="0 0 100 100">
                        <path d="M0,90 Q20,85 40,75 T60,60 T80,70 T100,40" fill="none" stroke="#38bdf8" stroke-dasharray="4,4" stroke-width="2" vector-effect="non-scaling-stroke"></path>
                    </svg>
                    <svg height="0" width="0">
                        <defs>
                            <linearGradient id="blue-gradient-admin" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#002293" stop-opacity="1"></stop>
                                <stop offset="100%" stop-color="#002293" stop-opacity="0"></stop>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Target & Status Summary (Stacked Cards) -->
    <div class="flex flex-col gap-6 h-full">
        <!-- Status Summary Solid Card -->
        <div class="bg-primary rounded-xl p-6 shadow-md text-white flex flex-col relative overflow-hidden h-44 justify-between">
            <div class="relative z-10">
                <h3 class="font-body-md text-body-md font-semibold opacity-90 uppercase tracking-wider text-xs">Status Summary</h3>
                <p class="font-body-sm text-xs opacity-75 mt-2">Active System Transactions</p>
                <p class="font-display-md text-3xl font-bold mt-1 font-data-mono">357 Processed</p>
            </div>
            <div class="relative z-10 flex items-center justify-between text-xs font-semibold pt-2 border-t border-white/20">
                <span>Ledger Integrity</span>
                <span class="px-2 py-0.5 rounded bg-emerald-500/30 text-emerald-300">100% Balanced</span>
            </div>
            <!-- Background Graphic Wave -->
            <div class="absolute bottom-0 left-0 right-0 h-24 opacity-20 pointer-events-none">
                <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                    <path d="M0,50 Q25,20 50,50 T100,50" fill="none" stroke="#ffffff" stroke-width="2" vector-effect="non-scaling-stroke"></path>
                </svg>
            </div>
        </div>

        <!-- Daily Target Circular Widget -->
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 shadow-sm flex-1 flex flex-col justify-center items-center text-center">
            <h3 class="font-headline-sm text-headline-sm text-on-surface mb-1 w-full text-left font-bold">Daily Target</h3>
            <p class="font-body-sm text-xs text-on-surface-variant w-full text-left mb-4">Platform Volume Milestone</p>
            <div class="relative w-32 h-32 flex items-center justify-center mb-3">
                <svg class="w-full h-full absolute inset-0 transform -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" fill="none" r="40" stroke="#e5e2e1" stroke-width="8"></circle>
                    <circle cx="50" cy="50" fill="none" r="40" stroke="#002293" stroke-dasharray="251.2" stroke-dashoffset="62.8" stroke-linecap="round" stroke-width="8"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="font-display-md text-2xl font-bold text-on-surface font-data-mono">75%</span>
                </div>
            </div>
            <div class="flex justify-between w-full px-2 mt-1 border-t border-outline-variant/40 pt-3">
                <div class="text-center">
                    <p class="font-body-sm text-xs text-on-surface-variant">Current</p>
                    <p class="font-label-bold text-xs text-on-surface font-data-mono font-bold mt-0.5">GH₵ 150.0k</p>
                </div>
                <div class="text-center">
                    <p class="font-body-sm text-xs text-on-surface-variant">Target</p>
                    <p class="font-label-bold text-xs text-on-surface font-data-mono font-bold mt-0.5">GH₵ 200.0k</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Global Platform Settings Control Card -->
<div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant mb-8 shadow-sm">
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
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden mb-8 shadow-sm">
    <div class="p-6 border-b border-outline-variant flex justify-between items-center">
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-bold">Platform Merchants, Custom Rates & KYB Directory</h3>
            <p class="font-body-sm text-xs text-on-surface-variant mt-0.5">Approve business KYC/KYB documents, suspend accounts, configure custom fee tiers, and adjust merchant balances.</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-primary/10 text-primary font-body-sm text-xs font-bold"><?= count($merchants) ?> Merchants</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase text-xs">
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
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden mb-8 shadow-sm">
    <div class="p-6 border-b border-outline-variant flex justify-between items-center">
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-bold">Platform Settlement Payout Clearances</h3>
            <p class="font-body-sm text-xs text-on-surface-variant mt-0.5">Review merchant payout requests and execute double-entry ledger settlement releases.</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase text-xs">
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
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden mb-8 shadow-sm">
    <div class="p-6 border-b border-outline-variant flex justify-between items-center">
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-bold">Global Disputes & Chargeback Resolution</h3>
            <p class="font-body-sm text-xs text-on-surface-variant mt-0.5">System-wide customer disputes requiring superadmin override resolution.</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-700 font-body-sm text-xs font-bold"><?= count($disputes) ?> System Disputes</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase text-xs">
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
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
    <div class="p-6 border-b border-outline-variant flex justify-between items-center">
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-bold">Global Immutable System Audit Trail</h3>
            <p class="font-body-sm text-xs text-on-surface-variant mt-0.5">Security logging for platform actions, balance adjustments, and operator clearances.</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-secondary/10 text-secondary font-body-sm text-xs font-bold">Live System Log</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase text-xs">
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
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
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
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
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
