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

<!-- Toolbar -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <form action="/payment-links" method="GET" class="relative w-full md:w-96">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
        <input type="text" name="search" class="w-full bg-surface-container-low border border-outline-variant rounded-xl pl-10 pr-4 py-2 font-body-sm text-body-sm focus:ring-2 focus:ring-secondary focus:bg-white transition-all" placeholder="Search payment link name..." value="<?= htmlspecialchars($search) ?>">
    </form>

    <button class="px-4 py-2 bg-primary text-on-primary font-body-sm text-body-sm font-semibold rounded-xl hover:bg-primary/90 transition-colors flex items-center gap-2 cursor-pointer shadow-sm" onclick="openModal('createLinkModal')">
        <span class="material-symbols-outlined text-[18px]">add</span>
        <span>Create Payment Link</span>
    </button>
</div>

<!-- Data Table Card -->
<div class="glass-card rounded-2xl border border-outline-variant overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase">
                    <th class="py-3.5 px-6 font-bold">Payment Link</th>
                    <th class="py-3.5 px-6 font-bold">Type</th>
                    <th class="py-3.5 px-6 font-bold">Amount</th>
                    <th class="py-3.5 px-6 font-bold">Usage Count</th>
                    <th class="py-3.5 px-6 font-bold">Status</th>
                    <th class="py-3.5 px-6 font-bold">Created Date</th>
                    <th class="py-3.5 px-6 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                <?php if (empty($links)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-12 text-on-surface-variant">
                            No payment links created yet. Click "Create Payment Link" to generate your first link.
                        </td>
                    </tr>
                <?php else: foreach ($links as $pl): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="py-4 px-6">
                            <a href="/payment-links/<?= $pl['id'] ?>/analytics" class="font-bold text-on-surface hover:text-secondary transition-colors block text-sm"><?= htmlspecialchars($pl['name']) ?></a>
                            <div class="font-data-mono text-[11px] text-on-surface-variant">/pay/<?= htmlspecialchars($pl['token']) ?></div>
                        </td>
                        <td class="py-4 px-6">
                            <?php if (($pl['link_type'] ?? 'one_time') === 'recurring_subscription'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 font-label-caps text-[10px] font-extrabold uppercase border border-indigo-200">
                                    <span class="material-symbols-outlined text-[14px]">sync</span>
                                    Subscription (<?= htmlspecialchars($pl['billing_interval'] ?? 'recurring') ?>)
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-label-caps text-[10px] font-extrabold uppercase border border-slate-200">
                                    <span class="material-symbols-outlined text-[14px]">payments</span>
                                    One-Time
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-6 font-data-mono font-bold text-on-surface">GH₵ <?= number_format($pl['amount'], 2) ?></td>
                        <td class="py-4 px-6 font-semibold text-on-surface"><?= number_format($pl['usage_count']) ?></td>
                        <td class="py-4 px-6"><?= Format::statusBadge($pl['status']) ?></td>
                        <td class="py-4 px-6 text-on-surface-variant font-data-mono"><?= Format::dateShort($pl['created_at']) ?></td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button class="p-2 rounded-xl hover:bg-surface-variant text-on-surface-variant hover:text-primary transition-colors inline-flex items-center" onclick="copyToClipboard('<?= (getenv('APP_URL') ?: 'http://localhost:8000') ?>/pay/<?= $pl['token'] ?>', 'Payment link copied!')" title="Copy URL">
                                    <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                </button>
                                <a href="/payment-links/<?= $pl['id'] ?>/analytics" class="p-2 rounded-xl hover:bg-surface-variant text-on-surface-variant hover:text-primary transition-colors inline-flex items-center" title="View Analytics">
                                    <span class="material-symbols-outlined text-[18px]">analytics</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Payment Link Modal -->
<div class="modal-overlay hidden fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" id="createLinkModal">
    <div class="bg-surface border border-outline-variant rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-outline-variant pb-3">
            <h3 class="font-headline-lg text-lg font-bold text-on-surface">Create Payment Link</h3>
            <button class="text-on-surface-variant hover:text-on-surface text-xl cursor-pointer" onclick="closeModal('createLinkModal')">&times;</button>
        </div>

        <form action="/payment-links/create" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">

            <!-- Link Type Selection -->
            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Payment Link Type *</label>
                <select name="link_type" id="linkTypeSelect" onchange="toggleSubscriptionPlanSelect(this.value)" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm font-semibold text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary cursor-pointer" required>
                    <option value="one_time" selected>💳 One-Time Payment Link</option>
                    <option value="recurring_subscription">🔄 Recurring Subscription Link</option>
                </select>
            </div>

            <!-- Subscription Plan Selector (Visible when Subscription Link chosen) -->
            <div id="subPlanContainer" class="hidden">
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Select Subscription Plan *</label>
                <select name="subscription_plan_id" id="subPlanSelect" onchange="onSubPlanChosen(this)" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary cursor-pointer">
                    <option value="">-- Select Subscription Tier --</option>
                    <?php foreach ($subscriptionPlans as $sp): ?>
                        <option value="<?= $sp['id'] ?>" data-name="<?= htmlspecialchars($sp['name']) ?>" data-amount="<?= $sp['amount'] ?>" data-interval="<?= htmlspecialchars($sp['billing_interval']) ?>">
                            <?= htmlspecialchars($sp['name']) ?> (GH₵ <?= number_format($sp['amount'], 2) ?>/<?= htmlspecialchars($sp['billing_interval']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Link Title / Product Name *</label>
                <input type="text" name="name" id="linkNameInput" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="e.g. Premium Consulting Package" required>
            </div>

            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Amount (GHS) *</label>
                <input type="number" step="0.01" name="amount" id="linkAmountInput" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm font-data-mono text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="250.00" required>
            </div>

            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Description (Optional)</label>
                <textarea name="description" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" rows="2" placeholder="Brief note for customer checkout page..."></textarea>
            </div>

            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Redirect URL (Optional)</label>
                <input type="url" name="redirect_url" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="https://yourwebsite.com/thank-you">
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-outline-variant">
                <button type="button" class="px-4 py-2 bg-surface-container-high text-on-surface font-body-sm text-xs font-bold rounded-xl hover:bg-surface-container-highest" onclick="closeModal('createLinkModal')">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm text-xs font-bold rounded-xl hover:bg-primary/90 shadow-sm cursor-pointer">Generate Link</button>
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

function toggleSubscriptionPlanSelect(val) {
    const container = document.getElementById('subPlanContainer');
    if (val === 'recurring_subscription') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}

function onSubPlanChosen(selectEl) {
    const selectedOpt = selectEl.options[selectEl.selectedIndex];
    if (selectedOpt && selectedOpt.dataset.name) {
        document.getElementById('linkNameInput').value = selectedOpt.dataset.name + ' Subscription';
        document.getElementById('linkAmountInput').value = parseFloat(selectedOpt.dataset.amount).toFixed(2);
    }
}
</script>
