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
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Subscription Management</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Automated recurring billing cycles, subscription tiers, and active subscriber tracking.</p>
    </div>
    <div class="flex items-center gap-3">
        <button class="px-4 py-2 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-body-sm text-body-sm font-semibold rounded-xl border border-outline-variant transition-colors flex items-center gap-2 cursor-pointer" onclick="openModal('createSubscriptionModal')">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            <span>Subscribe Customer</span>
        </button>
        <button class="px-4 py-2 bg-primary text-on-primary font-body-sm text-body-sm font-semibold rounded-xl hover:bg-primary/90 transition-colors flex items-center gap-2 cursor-pointer shadow-sm" onclick="openModal('createPlanModal')">
            <span class="material-symbols-outlined text-[18px]">add</span>
            <span>Create Plan</span>
        </button>
    </div>
</div>

<!-- Plan Cards Bento Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <?php if (empty($plans)): ?>
        <div class="p-6 rounded-2xl bg-surface-container border border-outline-variant col-span-3 text-center text-on-surface-variant">
            <span class="material-symbols-outlined text-[32px] text-on-surface-variant mb-2">receipt</span>
            <p class="font-bold text-sm">No Subscription Plans Found</p>
            <p class="text-xs">Create your first recurring subscription plan using the button above.</p>
        </div>
    <?php else: foreach ($plans as $p): ?>
        <div class="glass-card rounded-2xl p-6 flex flex-col justify-between h-44 relative border border-outline-variant group">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="font-headline-md text-base text-on-surface font-bold"><?= htmlspecialchars($p['name']) ?></h4>
                    <?php if (!empty($p['description'])): ?>
                        <p class="font-body-sm text-xs text-on-surface-variant line-clamp-1 mt-0.5"><?= htmlspecialchars($p['description']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-2">
                    <?= Format::statusBadge($p['status']) ?>
                    <form method="POST" action="/subscriptions/plan/delete/<?= $p['id'] ?>" onsubmit="return confirm('Delete this plan tier?')">
                        <button type="submit" class="text-on-surface-variant hover:text-rose-600 transition-colors p-1" title="Delete Plan">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </form>
                </div>
            </div>
            <div class="pt-4 border-t border-outline-variant flex justify-between items-end">
                <div>
                    <span class="font-label-caps text-[10px] text-on-surface-variant uppercase tracking-wider block">Billing Rate</span>
                    <div class="font-headline-lg text-2xl text-secondary font-bold font-data-mono">
                        GH₵ <?= number_format($p['amount'], 2) ?>
                        <span class="font-body-sm text-xs text-on-surface-variant font-normal">/ <?= htmlspecialchars($p['billing_interval']) ?></span>
                    </div>
                </div>
                <?php if (!empty($p['trial_days']) && $p['trial_days'] > 0): ?>
                    <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full"><?= $p['trial_days'] ?> Days Trial</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<!-- Active Subscribers Table Card -->
<div class="bg-surface rounded-2xl border border-outline-variant overflow-hidden shadow-sm">
    <div class="p-5 border-b border-outline-variant bg-surface-container-low flex justify-between items-center">
        <div>
            <h3 class="font-headline-md text-base text-on-surface font-bold">Active Recurring Subscribers</h3>
            <p class="font-body-sm text-xs text-on-surface-variant">Real-time status of active customer subscriptions and next billing dates.</p>
        </div>
        <span class="px-2.5 py-1 rounded-full bg-primary/10 text-primary font-body-sm text-xs font-bold"><?= count($subscriptions) ?> Subscribers</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/60 font-label-caps text-[11px] text-on-surface-variant uppercase tracking-wider">
                    <th class="py-3.5 px-6 font-bold">Customer</th>
                    <th class="py-3.5 px-6 font-bold">Plan Tier</th>
                    <th class="py-3.5 px-6 font-bold">Amount</th>
                    <th class="py-3.5 px-6 font-bold">Billing Cycle</th>
                    <th class="py-3.5 px-6 font-bold">Status</th>
                    <th class="py-3.5 px-6 font-bold">Next Billing Date</th>
                    <th class="py-3.5 px-6 font-bold text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-xs">
                <?php if (empty($subscriptions)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-12 text-on-surface-variant">
                            <span class="material-symbols-outlined text-[32px] block mb-2 text-on-surface-variant">group</span>
                            <p class="font-bold text-sm">No Active Subscribers</p>
                            <p class="text-xs text-on-surface-variant mt-0.5">Subscribe a customer using the "Subscribe Customer" button.</p>
                        </td>
                    </tr>
                <?php else: foreach ($subscriptions as $s): ?>
                    <tr class="hover:bg-surface-container-high/50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="font-bold text-on-surface text-sm"><?= htmlspecialchars($s['customer_name']) ?></div>
                            <div class="font-data-mono text-[11px] text-on-surface-variant"><?= htmlspecialchars($s['customer_email']) ?></div>
                        </td>
                        <td class="py-4 px-6 font-semibold text-on-surface"><?= htmlspecialchars($s['plan_name']) ?></td>
                        <td class="py-4 px-6 font-data-mono font-bold text-on-surface">GH₵ <?= number_format($s['amount'], 2) ?></td>
                        <td class="py-4 px-6 text-on-surface-variant capitalize">
                            <span class="px-2 py-0.5 rounded bg-surface-container-high font-semibold text-[11px]"><?= htmlspecialchars($s['billing_interval']) ?></span>
                        </td>
                        <td class="py-4 px-6"><?= Format::statusBadge($s['status']) ?></td>
                        <td class="py-4 px-6 font-data-mono text-on-surface-variant"><?= Format::dateShort($s['next_billing_date']) ?></td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form method="POST" action="/subscriptions/pause/<?= $s['id'] ?>">
                                    <button type="submit" class="px-2.5 py-1 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-body-sm text-xs font-bold rounded-lg border border-outline-variant transition-all inline-flex items-center gap-1" title="<?= $s['status'] === 'active' ? 'Pause Subscription' : 'Resume Subscription' ?>">
                                        <span class="material-symbols-outlined text-[14px]"><?= $s['status'] === 'active' ? 'pause' : 'play_arrow' ?></span>
                                        <span><?= $s['status'] === 'active' ? 'Pause' : 'Resume' ?></span>
                                    </button>
                                </form>

                                <?php if ($s['status'] !== 'cancelled'): ?>
                                    <form method="POST" action="/subscriptions/cancel/<?= $s['id'] ?>" onsubmit="return confirm('Cancel this customer subscription?')">
                                        <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-body-sm text-xs font-bold rounded-lg border border-rose-200 transition-all inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">cancel</span>
                                            <span>Cancel</span>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal 1: Create Plan Modal -->
<div class="modal-overlay hidden fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" id="createPlanModal">
    <div class="bg-surface border border-outline-variant rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-outline-variant pb-3">
            <h3 class="font-headline-lg text-lg font-bold text-on-surface">Create Subscription Plan</h3>
            <button class="text-on-surface-variant hover:text-on-surface text-xl cursor-pointer" onclick="closeModal('createPlanModal')">&times;</button>
        </div>
        <form action="/subscriptions/plan/create" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Plan Tier Name *</label>
                <input type="text" name="name" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="Pro Monthly Tier" required>
            </div>

            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Description (Optional)</label>
                <input type="text" name="description" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="Full platform features and high volume discounts">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Amount (GHS) *</label>
                    <input type="number" step="0.01" name="amount" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm font-data-mono text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="250.00" required>
                </div>

                <div>
                    <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Billing Interval *</label>
                    <select name="billing_interval" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary cursor-pointer" required>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Free Trial Days</label>
                <input type="number" name="trial_days" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="7" value="0">
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-outline-variant">
                <button type="button" class="px-4 py-2 bg-surface-container-high text-on-surface font-body-sm text-xs font-bold rounded-xl hover:bg-surface-container-highest" onclick="closeModal('createPlanModal')">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm text-xs font-bold rounded-xl hover:bg-primary/90 shadow-sm">Save Plan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Subscribe Customer Modal -->
<div class="modal-overlay hidden fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" id="createSubscriptionModal">
    <div class="bg-surface border border-outline-variant rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-outline-variant pb-3">
            <h3 class="font-headline-lg text-lg font-bold text-on-surface">Subscribe Customer</h3>
            <button class="text-on-surface-variant hover:text-on-surface text-xl cursor-pointer" onclick="closeModal('createSubscriptionModal')">&times;</button>
        </div>
        <form action="/subscriptions/create" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            
            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Select Customer *</label>
                <select name="customer_id" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary cursor-pointer" required>
                    <option value="">-- Choose Customer --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Select Subscription Plan *</label>
                <select name="plan_id" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary cursor-pointer" required>
                    <option value="">-- Choose Subscription Plan --</option>
                    <?php foreach ($plans as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> - GH₵ <?= number_format($p['amount'], 2) ?>/<?= htmlspecialchars($p['billing_interval']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Next Billing Date *</label>
                <input type="date" name="next_billing_date" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm font-data-mono text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" required>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-outline-variant">
                <button type="button" class="px-4 py-2 bg-surface-container-high text-on-surface font-body-sm text-xs font-bold rounded-xl hover:bg-surface-container-highest" onclick="closeModal('createSubscriptionModal')">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm text-xs font-bold rounded-xl hover:bg-primary/90 shadow-sm">Activate Subscription</button>
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
</script>
