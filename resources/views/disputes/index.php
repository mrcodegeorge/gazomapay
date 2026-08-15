<div class="space-y-6">
    
    <!-- Top Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-headline-lg text-2xl font-bold text-on-surface dark:text-inverse-on-surface">Disputes &amp; Chargebacks</h1>
            <p class="font-body-md text-sm text-on-surface-variant dark:text-surface-variant">Monitor customer payment disputes, submit evidence, and manage chargeback deadlines.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/developer" class="px-4 py-2 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-body-sm text-xs font-bold rounded-xl transition-all flex items-center gap-2 border border-outline-variant">
                <span class="material-symbols-outlined text-[18px]">help</span>
                <span>Dispute Policies</span>
            </a>
        </div>
    </div>

    <!-- Bento Metric Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Needs Response -->
        <div class="p-5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-900 dark:text-amber-200">
            <div class="flex items-center justify-between mb-3">
                <span class="font-label-caps text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">Needs Response</span>
                <span class="material-symbols-outlined text-amber-500 text-[22px]">warning</span>
            </div>
            <div class="font-data-mono text-2xl font-black mb-1"><?= number_format($metrics['needs_response_count'] ?? 0) ?></div>
            <p class="font-body-sm text-xs text-amber-700/80 dark:text-amber-300">GH₵ <?= number_format($metrics['needs_response_amount'] ?? 0, 2) ?> pending merchant response</p>
        </div>

        <!-- Under Review -->
        <div class="p-5 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-900 dark:text-blue-200">
            <div class="flex items-center justify-between mb-3">
                <span class="font-label-caps text-xs font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">Under Review</span>
                <span class="material-symbols-outlined text-blue-500 text-[22px]">history</span>
            </div>
            <div class="font-data-mono text-2xl font-black mb-1"><?= number_format($metrics['under_review_count'] ?? 0) ?></div>
            <p class="font-body-sm text-xs text-blue-700/80 dark:text-blue-300">Awaiting issuing bank decision</p>
        </div>

        <!-- Won Disputes -->
        <div class="p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-900 dark:text-emerald-200">
            <div class="flex items-center justify-between mb-3">
                <span class="font-label-caps text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Disputes Won</span>
                <span class="material-symbols-outlined text-emerald-500 text-[22px]">emoji_events</span>
            </div>
            <div class="font-data-mono text-2xl font-black mb-1"><?= number_format($metrics['won_count'] ?? 0) ?></div>
            <p class="font-body-sm text-xs text-emerald-700/80 dark:text-emerald-300">Chargebacks resolved in your favor</p>
        </div>

        <!-- Total Disputed Volume -->
        <div class="p-5 rounded-2xl bg-surface-container border border-outline-variant text-on-surface">
            <div class="flex items-center justify-between mb-3">
                <span class="font-label-caps text-xs font-bold uppercase tracking-wider text-on-surface-variant">Total Disputed Volume</span>
                <span class="material-symbols-outlined text-on-surface-variant text-[22px]">gavel</span>
            </div>
            <div class="font-data-mono text-2xl font-black mb-1">GH₵ <?= number_format($metrics['total_disputed_amount'] ?? 0, 2) ?></div>
            <p class="font-body-sm text-xs text-on-surface-variant"><?= number_format($metrics['total_disputes'] ?? 0) ?> total dispute claims filed</p>
        </div>
    </div>

    <!-- Filter & Search Bar Container -->
    <div class="bg-surface border border-outline-variant rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
        
        <!-- Status Tabs -->
        <div class="flex items-center gap-1 overflow-x-auto pb-1 md:pb-0">
            <?php
            $tabs = [
                'all' => 'All Disputes',
                'needs_response' => 'Needs Response',
                'under_review' => 'Under Review',
                'won' => 'Won',
                'accepted' => 'Lost / Accepted'
            ];
            foreach ($tabs as $key => $label):
                $isActive = ($currentStatus === $key);
            ?>
                <a href="/disputes?status=<?= $key ?>&search=<?= urlencode($search) ?>" 
                   class="px-3.5 py-1.5 rounded-xl font-body-sm text-xs font-bold transition-all whitespace-nowrap <?= $isActive ? 'bg-primary text-on-primary shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Search Form -->
        <form method="GET" action="/disputes" class="relative min-w-[240px]">
            <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                   placeholder="Search dispute code, ref, customer..." 
                   class="w-full pl-9 pr-4 py-2 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-xs text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary transition-all">
        </form>
    </div>

    <!-- Disputes Data Table -->
    <div class="bg-surface border border-outline-variant rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-outline-variant bg-surface-container-low font-label-caps text-[11px] text-on-surface-variant uppercase tracking-wider">
                        <th class="py-3.5 px-4 font-bold">Dispute Code</th>
                        <th class="py-3.5 px-4 font-bold">Transaction Ref</th>
                        <th class="py-3.5 px-4 font-bold">Customer</th>
                        <th class="py-3.5 px-4 font-bold">Reason</th>
                        <th class="py-3.5 px-4 font-bold text-right">Disputed Amount</th>
                        <th class="py-3.5 px-4 font-bold">Due Date</th>
                        <th class="py-3.5 px-4 font-bold">Status</th>
                        <th class="py-3.5 px-4 font-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant font-body-sm text-xs">
                    <?php if (empty($disputes)): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-on-surface-variant">
                                <div class="w-12 h-12 rounded-full bg-surface-container-high flex items-center justify-center mx-auto mb-3 text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[24px]">gavel</span>
                                </div>
                                <p class="font-bold text-sm">No disputes found</p>
                                <p class="text-xs text-on-surface-variant mt-1">There are no chargeback disputes matching your filter criteria.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($disputes as $d): ?>
                            <tr class="hover:bg-surface-container-high/50 transition-colors">
                                <td class="py-3.5 px-4 font-data-mono font-bold text-primary">
                                    <a href="/disputes/<?= $d['id'] ?>" class="hover:underline">
                                        <?= htmlspecialchars($d['dispute_code']) ?>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 font-data-mono text-on-surface">
                                    <a href="/transactions?search=<?= urlencode($d['transaction_reference']) ?>" class="hover:underline font-semibold">
                                        <?= htmlspecialchars($d['transaction_reference']) ?>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-on-surface">
                                    <div class="font-bold"><?= htmlspecialchars($d['customer_name'] ?? 'Guest Customer') ?></div>
                                    <div class="text-[11px] text-on-surface-variant font-data-mono"><?= htmlspecialchars($d['customer_email'] ?? 'n/a') ?></div>
                                </td>
                                <td class="py-3.5 px-4 text-on-surface capitalize">
                                    <span class="px-2.5 py-1 rounded-lg bg-surface-container-high font-semibold text-[11px]">
                                        <?= str_replace('_', ' ', $d['reason']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-data-mono font-bold text-on-surface">
                                    GH₵ <?= number_format($d['amount'], 2) ?>
                                </td>
                                <td class="py-3.5 px-4 font-data-mono text-on-surface-variant">
                                    <?= date('M d, Y', strtotime($d['due_date'])) ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php
                                    $statusBadges = [
                                        'needs_response' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'under_review' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'won' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'lost' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        'accepted' => 'bg-slate-100 text-slate-700 border-slate-200'
                                    ];
                                    $badgeClass = $statusBadges[$d['status']] ?? 'bg-slate-100 text-slate-700';
                                    ?>
                                    <span class="px-2.5 py-1 rounded-full font-label-caps text-[10px] font-extrabold uppercase border <?= $badgeClass ?>">
                                        <?= str_replace('_', ' ', $d['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="/disputes/<?= $d['id'] ?>" class="px-3 py-1.5 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-body-sm text-xs font-bold rounded-xl transition-all inline-flex items-center gap-1 border border-outline-variant">
                                        <span>View</span>
                                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
