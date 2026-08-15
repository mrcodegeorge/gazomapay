<!-- Header & Toolbar -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <form action="/transactions" method="GET" class="relative w-full md:w-96">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
        <input type="text" name="search" class="w-full bg-surface-container-low border border-outline-variant rounded-lg pl-10 pr-4 py-2 font-body-sm text-body-sm focus:ring-2 focus:ring-secondary focus:bg-white transition-all" placeholder="Search reference, customer name, email..." value="<?= htmlspecialchars($search) ?>">
        <input type="hidden" name="status" value="<?= htmlspecialchars($statusTab) ?>">
    </form>

    <div class="flex items-center gap-3 w-full md:w-auto justify-end">
        <a href="/transactions/export" class="px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-sm text-body-sm font-semibold text-on-surface hover:bg-surface-container-low transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">download</span>
            Export CSV
        </a>
    </div>
</div>

<!-- Status Filter Tabs -->
<div class="flex border-b border-outline-variant mb-6 gap-2">
    <a href="/transactions?status=all<?= $search ? '&search='.urlencode($search) : '' ?>" class="px-4 py-2 font-body-sm font-semibold text-body-sm border-b-2 transition-colors <?= $statusTab === 'all' ? 'border-secondary text-secondary' : 'border-transparent text-on-surface-variant hover:text-on-surface' ?>">All Payments</a>
    <a href="/transactions?status=successful<?= $search ? '&search='.urlencode($search) : '' ?>" class="px-4 py-2 font-body-sm font-semibold text-body-sm border-b-2 transition-colors <?= $statusTab === 'successful' ? 'border-secondary text-secondary' : 'border-transparent text-on-surface-variant hover:text-on-surface' ?>">Successful</a>
    <a href="/transactions?status=pending<?= $search ? '&search='.urlencode($search) : '' ?>" class="px-4 py-2 font-body-sm font-semibold text-body-sm border-b-2 transition-colors <?= $statusTab === 'pending' ? 'border-secondary text-secondary' : 'border-transparent text-on-surface-variant hover:text-on-surface' ?>">Pending</a>
    <a href="/transactions?status=failed<?= $search ? '&search='.urlencode($search) : '' ?>" class="px-4 py-2 font-body-sm font-semibold text-body-sm border-b-2 transition-colors <?= $statusTab === 'failed' ? 'border-secondary text-secondary' : 'border-transparent text-on-surface-variant hover:text-on-surface' ?>">Failed</a>
</div>

<!-- Data Table Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase">
                    <th class="py-3.5 px-6">Transaction Ref</th>
                    <th class="py-3.5 px-6">Customer</th>
                    <th class="py-3.5 px-6">Amount</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6">Created Date</th>
                    <th class="py-3.5 px-6 text-right">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-12 text-on-surface-variant">
                            No transactions found matching your criteria.
                        </td>
                    </tr>
                <?php else: foreach ($transactions as $tx): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="py-4 px-6 font-data-mono text-secondary font-semibold">
                            <a href="/transactions/<?= $tx['id'] ?>" class="hover:underline"><?= htmlspecialchars($tx['reference']) ?></a>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-medium text-on-surface"><?= htmlspecialchars($tx['customer_name'] ?? 'Guest Customer') ?></div>
                            <div class="font-label-caps text-[11px] text-on-surface-variant"><?= htmlspecialchars($tx['customer_email'] ?? '') ?></div>
                        </td>
                        <td class="py-4 px-6 font-data-mono font-bold text-on-surface">GH₵ <?= number_format($tx['amount'], 2) ?></td>
                        <td class="py-4 px-6"><?= Format::statusBadge($tx['status']) ?></td>
                        <td class="py-4 px-6 text-on-surface-variant"><?= Format::date($tx['created_at']) ?></td>
                        <td class="py-4 px-6 text-right">
                            <a href="/transactions/<?= $tx['id'] ?>" class="p-1.5 rounded hover:bg-surface-variant text-on-surface-variant hover:text-primary transition-colors inline-flex items-center" title="View details">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="p-4 border-t border-outline-variant bg-surface-bright flex flex-col sm:flex-row justify-between items-center gap-3 font-body-sm text-on-surface-variant">
        <div>
            Showing <span class="font-medium text-on-surface"><?= min(1, $totalRecords) ?></span> to <span class="font-medium text-on-surface"><?= min($limit * $currentPage, $totalRecords) ?></span> of <span class="font-medium text-on-surface"><?= $totalRecords ?></span> results
        </div>
        <div class="flex items-center gap-1">
            <?php if ($currentPage > 1): ?>
                <a href="/transactions?page=<?= $currentPage - 1 ?>&status=<?= $statusTab ?>&search=<?= urlencode($search) ?>" class="px-3 py-1 border border-outline-variant rounded hover:bg-surface-container-low text-on-surface">&larr;</a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= min(5, $totalPages); $p++): ?>
                <a href="/transactions?page=<?= $p ?>&status=<?= $statusTab ?>&search=<?= urlencode($search) ?>" class="px-3 py-1 border rounded <?= $p === $currentPage ? 'bg-primary text-on-primary border-primary font-bold' : 'border-outline-variant text-on-surface hover:bg-surface-container-low' ?>"><?= $p ?></a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="/transactions?page=<?= $currentPage + 1 ?>&status=<?= $statusTab ?>&search=<?= urlencode($search) ?>" class="px-3 py-1 border border-outline-variant rounded hover:bg-surface-container-low text-on-surface">&rarr;</a>
            <?php endif; ?>
        </div>
    </div>
</div>
