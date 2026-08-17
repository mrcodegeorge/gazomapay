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
        <div class="flex items-center gap-3">
            <h2 class="font-display-md text-display-md md:text-display-lg font-bold text-on-surface">Merchant Approvals</h2>
            <span class="bg-primary text-on-primary text-xs font-bold px-2.5 py-0.5 rounded-full flex items-center justify-center min-w-[24px] font-data-mono"><?= count($merchants) ?></span>
        </div>
        <p class="text-on-surface-variant font-body-md text-body-md mt-1">Review business KYC/KYB registration documents, manage onboarding requests, set custom fee tiers, and adjust merchant balances.</p>
    </div>
    <button onclick="window.print()" class="bg-surface-container-lowest border border-outline-variant text-on-surface font-label-bold text-xs px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-surface-container-low transition-colors cursor-pointer">
        <span class="material-symbols-outlined text-[18px]">download</span>
        Export List
    </button>
</div>

<!-- Search & Filter Controls Card -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-4 mb-6 flex flex-col md:flex-row gap-4 items-center shadow-sm">
    <form method="GET" action="/admin/merchants" class="relative w-full md:w-96">
        <input type="hidden" name="kyc_status" value="<?= htmlspecialchars($kycFilter) ?>">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="w-full pl-10 pr-4 py-2 bg-surface-container-low border border-outline-variant rounded-lg font-body-md text-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all h-[40px]" placeholder="Search by merchant name, email, or ID..." />
    </form>
    <div class="flex w-full md:w-auto gap-3 flex-wrap">
        <div class="relative min-w-[160px] flex-1 md:flex-none">
            <select onchange="window.location.href='/admin/merchants?kyc_status=' + this.value" class="w-full appearance-none bg-surface-container-low border border-outline-variant rounded-lg px-4 py-2 pr-10 font-body-md text-sm text-on-surface focus:outline-none focus:border-primary h-[40px] cursor-pointer">
                <option value="all" <?= $kycFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                <option value="verification_pending" <?= $kycFilter === 'verification_pending' ? 'selected' : '' ?>>Pending Clearance</option>
                <option value="approved" <?= $kycFilter === 'approved' ? 'selected' : '' ?>>Approved KYB</option>
                <option value="rejected" <?= $kycFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-[20px]">expand_more</span>
        </div>
    </div>
</div>

<!-- Main 2-Column Application Queue & Detailed Reviewer Drawer -->
<div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
    <!-- Left Column: Application Queue Table -->
    <div class="xl:col-span-8 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface-bright">
            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Application Queue</h3>
            <span class="text-xs font-body-sm text-on-surface-variant font-data-mono">Showing <?= count($merchants) ?> Merchants</span>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low/80 border-b border-outline-variant text-xs uppercase font-label-caps text-on-surface-variant">
                        <th class="p-4 whitespace-nowrap">Merchant Name</th>
                        <th class="p-4 whitespace-nowrap">Business Type</th>
                        <th class="p-4 whitespace-nowrap">Available Bal</th>
                        <th class="p-4 whitespace-nowrap">Fee Structure</th>
                        <th class="p-4 whitespace-nowrap">KYB Status</th>
                        <th class="p-4 text-center whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant font-body-sm text-sm">
                    <?php if (empty($merchants)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-12 text-on-surface-variant">No merchant application records found matching criteria.</td>
                        </tr>
                    <?php else: foreach ($merchants as $idx => $m): ?>
                        <tr class="hover:bg-surface-container-low/50 transition-colors group cursor-pointer merchant-row <?= $idx === 0 ? 'bg-primary/5' : '' ?>" onclick="selectMerchant(<?= htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8') ?>)">
                            <td class="p-4">
                                <div class="font-bold text-on-surface"><?= htmlspecialchars($m['name']) ?></div>
                                <div class="font-data-mono text-xs text-on-surface-variant mt-0.5"><?= htmlspecialchars($m['merchant_id']) ?></div>
                            </td>
                            <td class="p-4 text-on-surface capitalize"><?= htmlspecialchars($m['business_type'] ?? 'Limited Company') ?></td>
                            <td class="p-4 font-data-mono font-bold text-[#10B981]">GH₵ <?= number_format($m['available_balance'], 2) ?></td>
                            <td class="p-4 font-data-mono text-xs text-on-surface-variant">
                                <?php if (!empty($m['custom_fee_percentage'])): ?>
                                    <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-bold border border-indigo-200"><?= number_format($m['custom_fee_percentage'], 2) ?>% + GH₵ <?= number_format($m['custom_fee_flat'] ?? 0.50, 2) ?></span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded bg-surface-container-high text-on-surface-variant">Default (1.50% + 0.50)</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4"><?= Format::statusBadge($m['kyc_status'] ?? 'approved') ?></td>
                            <td class="p-4 text-center" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" class="p-1 text-primary hover:bg-primary/10 rounded transition-colors" title="Review Documents" onclick="selectMerchant(<?= htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8') ?>)">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </button>
                                    <button type="button" class="p-1 text-on-surface-variant hover:bg-surface-container-high rounded transition-colors" title="Adjust Balance" onclick="openBalanceModal(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['name'])) ?>', <?= $m['available_balance'] ?>)">
                                        <span class="material-symbols-outlined text-[18px]">account_balance</span>
                                    </button>
                                    <button type="button" class="p-1 text-on-surface-variant hover:bg-surface-container-high rounded transition-colors" title="Set Fee Rate" onclick="openFeeModal(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['name'])) ?>', '<?= $m['custom_fee_percentage'] ?? '' ?>', '<?= $m['custom_fee_flat'] ?? '' ?>')">
                                        <span class="material-symbols-outlined text-[18px]">percent</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column: Detailed Merchant Review Side Panel -->
    <div class="xl:col-span-4 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm flex flex-col sticky top-24">
        <?php $activeM = $merchants[0] ?? null; ?>
        <?php if ($activeM): ?>
            <div class="p-6 border-b border-outline-variant bg-surface-bright flex flex-col gap-2 rounded-t-xl" id="drawerHeader">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface" id="mNameHeader"><?= htmlspecialchars($activeM['name']) ?></h3>
                        <p class="text-xs font-data-mono text-on-surface-variant" id="mIdHeader">Application #<?= htmlspecialchars($activeM['merchant_id']) ?></p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-500/10 text-emerald-700 border border-emerald-200">Risk: Low</span>
                </div>
            </div>

            <div class="p-6 flex flex-col gap-6 overflow-y-auto max-h-[calc(100vh-250px)]">
                <!-- Section 1: Contact Information -->
                <section>
                    <h4 class="font-label-caps text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-primary">contact_mail</span> Contact Information
                    </h4>
                    <div class="grid grid-cols-1 gap-y-3 font-body-sm text-xs">
                        <div>
                            <div class="text-on-surface-variant font-semibold">Primary Contact / Admin</div>
                            <div class="text-sm font-bold text-on-surface mt-0.5" id="mContactName"><?= htmlspecialchars($activeM['name']) ?></div>
                        </div>
                        <div>
                            <div class="text-on-surface-variant font-semibold">Email Address</div>
                            <div class="text-sm font-data-mono text-on-surface mt-0.5" id="mContactEmail"><?= htmlspecialchars($activeM['email']) ?></div>
                        </div>
                        <div>
                            <div class="text-on-surface-variant font-semibold">Business Type</div>
                            <div class="text-sm text-on-surface mt-0.5 capitalize" id="mBusinessType"><?= htmlspecialchars($activeM['business_type'] ?? 'Limited Liability Company') ?></div>
                        </div>
                    </div>
                </section>

                <hr class="border-outline-variant/60" />

                <!-- Section 2: Business Documents -->
                <section>
                    <h4 class="font-label-caps text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-primary">description</span> KYB Business Documents
                    </h4>
                    <div class="flex flex-col gap-2 font-body-sm text-xs">
                        <div class="flex items-center justify-between p-2.5 rounded-lg border border-outline-variant bg-surface-container-low hover:bg-surface-container-high transition-colors cursor-pointer">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-secondary text-[20px]">picture_as_pdf</span>
                                <span class="font-semibold text-on-surface">Certificate_of_Inc.pdf</span>
                            </div>
                            <span class="material-symbols-outlined text-on-surface-variant text-[18px]">download</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-lg border border-outline-variant bg-surface-container-low hover:bg-surface-container-high transition-colors cursor-pointer">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-secondary text-[20px]">picture_as_pdf</span>
                                <span class="font-semibold text-on-surface">Tax_ID_Verification.pdf</span>
                            </div>
                            <span class="material-symbols-outlined text-on-surface-variant text-[18px]">download</span>
                        </div>
                    </div>
                </section>

                <hr class="border-outline-variant/60" />

                <!-- Section 3: Verification Checklist -->
                <section>
                    <h4 class="font-label-caps text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-primary">checklist</span> Verification Checklist
                    </h4>
                    <div class="flex flex-col gap-3 font-body-sm text-xs">
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox" checked class="mt-0.5 rounded border-outline-variant text-primary focus:ring-primary h-4 w-4 bg-surface" />
                            <span class="text-on-surface group-hover:text-primary transition-colors font-medium">Identity verification passed (KYC)</span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox" checked class="mt-0.5 rounded border-outline-variant text-primary focus:ring-primary h-4 w-4 bg-surface" />
                            <span class="text-on-surface group-hover:text-primary transition-colors font-medium">Business registry confirmed (KYB)</span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox" checked class="mt-0.5 rounded border-outline-variant text-primary focus:ring-primary h-4 w-4 bg-surface" />
                            <span class="text-on-surface group-hover:text-primary transition-colors font-medium">Bank account ownership validated</span>
                        </label>
                    </div>
                </section>
            </div>

            <!-- Action Footer -->
            <div class="p-6 border-t border-outline-variant bg-surface-bright mt-auto rounded-b-xl flex gap-3" id="drawerActions">
                <?php if (($activeM['kyc_status'] ?? 'approved') !== 'approved'): ?>
                    <form method="POST" action="/admin/merchants/<?= $activeM['id'] ?>/reject-kyc" class="flex-1">
                        <button type="submit" class="w-full bg-rose-50 border border-rose-300 text-rose-700 font-bold text-xs py-2.5 rounded-lg hover:bg-rose-100 transition-colors flex items-center justify-center gap-2 cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">close</span> Reject
                        </button>
                    </form>
                    <form method="POST" action="/admin/merchants/<?= $activeM['id'] ?>/approve-kyc" class="flex-1">
                        <button type="submit" class="w-full bg-emerald-600 border border-transparent text-white font-bold text-xs py-2.5 rounded-lg hover:bg-emerald-500 shadow-sm transition-colors flex items-center justify-center gap-2 cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">check</span> Approve
                        </button>
                    </form>
                <?php else: ?>
                    <div class="w-full py-2.5 text-center bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg font-bold text-xs flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">verified</span> KYB Verification Approved
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="p-8 text-center text-on-surface-variant font-body-sm text-sm">Select a merchant from the left queue to review documents.</div>
        <?php endif; ?>
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
function selectMerchant(m) {
    document.getElementById('mNameHeader').innerText = m.name;
    document.getElementById('mIdHeader').innerText = 'Application #' + m.merchant_id;
    document.getElementById('mContactName').innerText = m.name;
    document.getElementById('mContactEmail').innerText = m.email;
    document.getElementById('mBusinessType').innerText = m.business_type || 'Limited Liability Company';

    const actionsDiv = document.getElementById('drawerActions');
    if ((m.kyc_status || 'approved') !== 'approved') {
        actionsDiv.innerHTML = `
            <form method="POST" action="/admin/merchants/${m.id}/reject-kyc" class="flex-1">
                <button type="submit" class="w-full bg-rose-50 border border-rose-300 text-rose-700 font-bold text-xs py-2.5 rounded-lg hover:bg-rose-100 transition-colors flex items-center justify-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">close</span> Reject
                </button>
            </form>
            <form method="POST" action="/admin/merchants/${m.id}/approve-kyc" class="flex-1">
                <button type="submit" class="w-full bg-emerald-600 border border-transparent text-white font-bold text-xs py-2.5 rounded-lg hover:bg-emerald-500 shadow-sm transition-colors flex items-center justify-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">check</span> Approve
                </button>
            </form>
        `;
    } else {
        actionsDiv.innerHTML = `
            <div class="w-full py-2.5 text-center bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg font-bold text-xs flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">verified</span> KYB Verification Approved
            </div>
        `;
    }
}

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
