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
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Global Platform Disputes & Chargebacks</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">System-wide customer disputes requiring superadmin override resolution.</p>
    </div>
</div>

<!-- Disputes Table Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase">
                    <th class="py-3.5 px-6">Dispute Code</th>
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
                        <td colspan="7" class="text-center py-12 text-on-surface-variant font-body-sm">No active disputes or chargebacks found.</td>
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
                                        <button type="submit" class="px-3 py-1 bg-emerald-50 text-emerald-700 font-body-sm text-xs font-semibold rounded border border-emerald-200 hover:bg-emerald-100 transition-colors cursor-pointer">Rule Won</button>
                                    </form>
                                    <form method="POST" action="/admin/disputes/<?= $d['id'] ?>/resolve">
                                        <input type="hidden" name="status" value="lost">
                                        <button type="submit" class="px-3 py-1 bg-rose-50 text-rose-700 font-body-sm text-xs font-semibold rounded border border-rose-200 hover:bg-rose-100 transition-colors cursor-pointer">Rule Lost</button>
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
