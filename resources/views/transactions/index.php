<div class="toolbar">
    <form action="/transactions" method="GET" class="search-box">
        <span class="search-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </span>
        <input type="text" name="search" class="search-input" placeholder="Search transactions..." value="<?= htmlspecialchars($search) ?>">
        <input type="hidden" name="status" value="<?= htmlspecialchars($statusTab) ?>">
    </form>

    <div style="display: flex; gap: 12px;">
        <button class="btn btn-outline" onclick="showToast('Advanced filter modal opened')">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            Filter
        </button>
        <a href="/transactions/export" class="btn btn-outline">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export
        </a>
    </div>
</div>

<!-- Status Tabs matching mockup -->
<div class="nav-tabs">
    <a href="/transactions?status=all<?= $search ? '&search='.urlencode($search) : '' ?>" class="tab-item <?= $statusTab === 'all' ? 'active' : '' ?>">All</a>
    <a href="/transactions?status=successful<?= $search ? '&search='.urlencode($search) : '' ?>" class="tab-item <?= $statusTab === 'successful' ? 'active' : '' ?>">Successful</a>
    <a href="/transactions?status=pending<?= $search ? '&search='.urlencode($search) : '' ?>" class="tab-item <?= $statusTab === 'pending' ? 'active' : '' ?>">Pending</a>
    <a href="/transactions?status=failed<?= $search ? '&search='.urlencode($search) : '' ?>" class="tab-item <?= $statusTab === 'failed' ? 'active' : '' ?>">Failed</a>
</div>

<!-- Transactions Table -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        No transactions found matching your criteria.
                    </td>
                </tr>
            <?php else: foreach ($transactions as $tx): ?>
                <tr>
                    <td style="font-weight: 600; font-family: monospace; color: var(--primary-blue);">
                        <a href="/transactions/<?= $tx['id'] ?>"><?= htmlspecialchars($tx['reference']) ?></a>
                    </td>
                    <td>
                        <div style="font-weight: 600;"><?= htmlspecialchars($tx['customer_name'] ?? 'Guest Customer') ?></div>
                        <div style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($tx['customer_email'] ?? '') ?></div>
                    </td>
                    <td style="font-weight: 700;">GH₵ <?= number_format($tx['amount'], 2) ?></td>
                    <td><?= Format::statusBadge($tx['status']) ?></td>
                    <td style="color: var(--text-muted); font-size: 13px;"><?= Format::date($tx['created_at']) ?></td>
                    <td style="text-align: right;">
                        <a href="/transactions/<?= $tx['id'] ?>" class="btn btn-outline btn-sm" title="View details">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>

    <!-- Pagination Bar -->
    <div class="pagination-bar">
        <div>
            Showing <?= min(1, $totalRecords) ?> to <?= min($limit * $currentPage, $totalRecords) ?> of <?= $totalRecords ?> results
        </div>
        <div class="pagination-pages">
            <?php if ($currentPage > 1): ?>
                <a href="/transactions?page=<?= $currentPage - 1 ?>&status=<?= $statusTab ?>&search=<?= urlencode($search) ?>" class="page-link">&lt;</a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= min(5, $totalPages); $p++): ?>
                <a href="/transactions?page=<?= $p ?>&status=<?= $statusTab ?>&search=<?= urlencode($search) ?>" class="page-link <?= $p === $currentPage ? 'active' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>

            <?php if ($totalPages > 5): ?>
                <span class="page-link" style="border: none;">...</span>
                <a href="/transactions?page=<?= $totalPages ?>&status=<?= $statusTab ?>&search=<?= urlencode($search) ?>" class="page-link"><?= $totalPages ?></a>
            <?php endif; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="/transactions?page=<?= $currentPage + 1 ?>&status=<?= $statusTab ?>&search=<?= urlencode($search) ?>" class="page-link">&gt;</a>
            <?php endif; ?>
        </div>
    </div>
</div>
