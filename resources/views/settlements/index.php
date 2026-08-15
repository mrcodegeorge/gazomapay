<!-- 3 Settlement Metric Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Available Balance Card -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-40 border-l-4 border-l-secondary">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Available Balance</span>
            <span class="material-symbols-outlined text-secondary">account_balance_wallet</span>
        </div>
        <div class="flex items-end justify-between">
            <div>
                <div class="font-headline-lg text-headline-lg text-on-surface font-bold">GH₵ <?= number_format($merchant['available_balance'], 2) ?></div>
                <span class="font-body-sm text-body-sm text-on-surface-variant">Ready for payout</span>
            </div>
            <button onclick="openModal('requestPayoutModal')" class="px-3.5 py-1.5 bg-secondary text-on-secondary font-body-sm text-xs font-semibold rounded-md hover:bg-secondary-container transition-colors cursor-pointer">
                Withdraw &rarr;
            </button>
        </div>
    </div>

    <!-- Pending Balance Card -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-40">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Pending Balance</span>
            <span class="material-symbols-outlined text-amber-500">hourglass_top</span>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg text-amber-600 font-bold">GH₵ <?= number_format($merchant['pending_balance'], 2) ?></div>
            <span class="font-body-sm text-body-sm text-on-surface-variant">Awaiting bank settlement cycle</span>
        </div>
    </div>

    <!-- Total Settled Balance Card -->
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-40">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Total Settled Balance</span>
            <span class="material-symbols-outlined text-[#10B981]">verified</span>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg text-[#10B981] font-bold">GH₵ <?= number_format($merchant['settled_balance'], 2) ?></div>
            <span class="font-body-sm text-body-sm text-on-surface-variant">Disbursed to bank accounts</span>
        </div>
    </div>
</div>

<!-- Settlement History Table Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant bg-surface flex justify-between items-center">
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-semibold">Settlement History</h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Log of requested bank payouts and automated settlement transfers.</p>
        </div>
        <button class="px-4 py-2 bg-primary text-on-primary font-body-sm text-body-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 cursor-pointer shadow-sm" onclick="openModal('requestPayoutModal')">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Request Settlement
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase">
                    <th class="py-3.5 px-6">Settlement Ref</th>
                    <th class="py-3.5 px-6">Gross Amount</th>
                    <th class="py-3.5 px-6">Fee</th>
                    <th class="py-3.5 px-6">Net Amount</th>
                    <th class="py-3.5 px-6">Bank Account</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6">Requested Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                <?php if (empty($settlements)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-12 text-on-surface-variant">No settlement requests recorded yet.</td>
                    </tr>
                <?php else: foreach ($settlements as $s): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="py-4 px-6 font-data-mono font-bold text-secondary"><?= htmlspecialchars($s['reference']) ?></td>
                        <td class="py-4 px-6 font-data-mono font-bold text-on-surface">GH₵ <?= number_format($s['gross_amount'], 2) ?></td>
                        <td class="py-4 px-6 font-data-mono text-on-surface-variant">GH₵ <?= number_format($s['fee'], 2) ?></td>
                        <td class="py-4 px-6 font-data-mono font-bold text-[#10B981]">GH₵ <?= number_format($s['net_amount'], 2) ?></td>
                        <td class="py-4 px-6">
                            <div class="font-medium text-on-surface"><?= htmlspecialchars($s['bank_name']) ?></div>
                            <div class="font-data-mono text-[11px] text-on-surface-variant"><?= htmlspecialchars($s['account_number']) ?></div>
                        </td>
                        <td class="py-4 px-6"><?= Format::statusBadge($s['status']) ?></td>
                        <td class="py-4 px-6 text-on-surface-variant"><?= Format::date($s['created_at']) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Request Payout Modal -->
<div class="modal-overlay" id="requestPayoutModal">
    <div class="modal-card max-w-[480px]">
        <div class="modal-header">
            <h3 class="modal-title">Request Bank Settlement</h3>
            <button class="modal-close" onclick="closeModal('requestPayoutModal')">&times;</button>
        </div>
        <form action="/settlements/request" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group mb-4">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Available Balance</label>
                <input type="text" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface-container font-data-mono font-bold text-on-surface" value="GH₵ <?= number_format($merchant['available_balance'], 2) ?>" readonly>
            </div>
            <div class="form-group mb-4">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Withdrawal Amount (GHS)</label>
                <input type="number" step="0.01" max="<?= $merchant['available_balance'] ?>" name="amount" class="w-full px-3 py-2 border border-outline-variant rounded font-body-md" value="<?= $merchant['available_balance'] ?>" required>
            </div>
            <div class="form-group mb-6">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Destination Bank Account</label>
                <select name="bank_name" class="w-full px-3 py-2 border border-outline-variant rounded bg-white font-body-sm">
                    <option value="GCB Bank Ghana - 1011129384728">GCB Bank Ghana (1011129384728)</option>
                    <option value="Stanbic Bank Ghana - 9040001827364">Stanbic Bank Ghana (9040001827364)</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" class="px-4 py-2 border border-outline-variant rounded font-body-sm text-on-surface hover:bg-surface-container-low" onclick="closeModal('requestPayoutModal')">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm font-medium rounded hover:bg-primary/90 cursor-pointer">Confirm Payout Request</button>
            </div>
        </form>
    </div>
</div>
