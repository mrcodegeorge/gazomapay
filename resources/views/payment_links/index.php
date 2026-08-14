<div class="toolbar">
    <form action="/payment-links" method="GET" class="search-box">
        <span class="search-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </span>
        <input type="text" name="search" class="search-input" placeholder="Search payment links..." value="<?= htmlspecialchars($search) ?>">
    </form>

    <button class="btn btn-primary" onclick="openModal('createLinkModal')">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create Link
    </button>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Link Name</th>
                <th>Amount</th>
                <th>Usage</th>
                <th>Status</th>
                <th>Created</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($links)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        No payment links created yet. Click "Create Link" to generate your first link.
                    </td>
                </tr>
            <?php else: foreach ($links as $pl): ?>
                <tr>
                    <td style="font-weight: 700;">
                        <a href="/payment-links/<?= $pl['id'] ?>/analytics" style="color: var(--text-main);"><?= htmlspecialchars($pl['name']) ?></a>
                        <div style="font-size: 11px; color: var(--text-muted); font-family: monospace;">/pay/<?= htmlspecialchars($pl['token']) ?></div>
                    </td>
                    <td style="font-weight: 700;">GH₵ <?= number_format($pl['amount'], 2) ?></td>
                    <td><?= number_format($pl['usage_count']) ?></td>
                    <td><?= Format::statusBadge($pl['status']) ?></td>
                    <td style="color: var(--text-muted); font-size: 13px;"><?= Format::dateShort($pl['created_at']) ?></td>
                    <td style="text-align: right; display: flex; gap: 8px; justify-content: flex-end;">
                        <button class="btn btn-outline btn-sm" onclick="copyToClipboard('<?= (getenv('APP_URL') ?: 'http://localhost:8000') ?>/pay/<?= $pl['token'] ?>', 'Payment link copied!')" title="Copy payment URL">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        </button>
                        <a href="/payment-links/<?= $pl['id'] ?>/analytics" class="btn btn-outline btn-sm" title="View analytics">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 012 2h2a2 2 0 012-2z"/></svg>
                        </a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Create Payment Link Modal -->
<div class="modal-overlay" id="createLinkModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Create Payment Link</h3>
            <button class="modal-close" onclick="closeModal('createLinkModal')">&times;</button>
        </div>
        <form action="/payment-links/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group">
                <label class="form-label">Link Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. iPhone 15 Payment" required>
            </div>
            <div class="form-group">
                <label class="form-label">Amount (GHS)</label>
                <input type="number" step="0.01" name="amount" class="form-control" placeholder="6500.00" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description (Optional)</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Item description or customer instructions..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Redirect URL (Optional)</label>
                <input type="url" name="redirect_url" class="form-control" placeholder="https://yourwebsite.com/thank-you">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('createLinkModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Payment Link</button>
            </div>
        </form>
    </div>
</div>
