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
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Merchants Directory & KYB Clearance Center</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Review business registration documents, grant KYB approvals, adjust merchant balances, and configure custom fee rates.</p>
    </div>
</div>

<!-- Filter Tabs & Search Bar Card -->
<div class="glass-card rounded-xl p-4 border border-outline-variant mb-6 bg-surface flex flex-col md:flex-row justify-between items-center gap-4">
    <!-- Filter Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto">
        <a href="/admin/merchants" class="px-3.5 py-1.5 rounded-lg font-body-sm text-xs font-bold transition-all <?= $kycFilter === 'all' ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high' ?>">All Merchants</a>
        <a href="/admin/merchants?kyc_status=verification_pending" class="px-3.5 py-1.5 rounded-lg font-body-sm text-xs font-bold transition-all <?= $kycFilter === 'verification_pending' ? 'bg-amber-600 text-white' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high' ?>">Pending Clearance</a>
        <a href="/admin/merchants?kyc_status=approved" class="px-3.5 py-1.5 rounded-lg font-body-sm text-xs font-bold transition-all <?= $kycFilter === 'approved' ? 'bg-emerald-600 text-white' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high' ?>">Approved KYB</a>
        <a href="/admin/merchants?kyc_status=rejected" class="px-3.5 py-1.5 rounded-lg font-body-sm text-xs font-bold transition-all <?= $kycFilter === 'rejected' ? 'bg-rose-600 text-white' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high' ?>">Rejected</a>
    </div>

    <!-- Search Input Form -->
    <form method="GET" action="/admin/merchants" class="w-full md:w-72 relative">
        <input type="hidden" name="kyc_status" value="<?= htmlspecialchars($kycFilter) ?>">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search merchant name or email..." class="w-full pl-9 pr-4 py-2 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-xs text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary">
        <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[16px]">search</span>
    </form>
</div>

<!-- Merchants Directory Table Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden">
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
                    <th class="py-3.5 px-6">Account Status</th>
                    <th class="py-3.5 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                <?php if (empty($merchants)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-12 text-on-surface-variant font-body-sm">No merchants found matching your query.</td>
                    </tr>
                <?php else: foreach ($merchants as $m): ?>
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
                        <td class="py-4 px-6"><?= Format::statusBadge($m['account_status'] ?? 'active') ?></td>
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
                <?php endforeach; endif; ?>
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
