<!-- Flash Notifications -->
<?php if ($msg = Response::getFlash('success')): ?>
    <div class="p-4 mb-6 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl font-body-sm text-sm flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
        <span><?= htmlspecialchars($msg) ?></span>
    </div>
<?php endif; ?>

<!-- Header Toolbar -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Platform Settlement Payout Clearances</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Review merchant payout requests and execute double-entry ledger settlement releases.</p>
    </div>
</div>

<!-- Status Filters -->
<div class="glass-card rounded-xl p-4 border border-outline-variant mb-6 bg-surface flex items-center gap-2">
    <a href="/admin/settlements" class="px-3.5 py-1.5 rounded-lg font-body-sm text-xs font-bold transition-all <?= $statusFilter === 'all' ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high' ?>">All Settlements</a>
    <a href="/admin/settlements?status=pending" class="px-3.5 py-1.5 rounded-lg font-body-sm text-xs font-bold transition-all <?= $statusFilter === 'pending' ? 'bg-amber-600 text-white' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high' ?>">Pending Clearance</a>
    <a href="/admin/settlements?status=completed" class="px-3.5 py-1.5 rounded-lg font-body-sm text-xs font-bold transition-all <?= $statusFilter === 'completed' ? 'bg-emerald-600 text-white' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high' ?>">Completed</a>
</div>

<!-- Settlements Table Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden">
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
                        <td colspan="8" class="text-center py-12 text-on-surface-variant font-body-sm">No settlement payout requests found.</td>
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
