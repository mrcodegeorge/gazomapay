<div style="margin-bottom: 20px;">
    <a href="/payment-links" style="font-size: 13px; font-weight: 600;">&larr; Back to Payment Links</a>
</div>

<!-- Header Info Bar -->
<div class="card" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <h2 style="font-size: 24px; font-weight: 800; color: var(--text-main);"><?= htmlspecialchars($link['name']) ?></h2>
                <?= Format::statusBadge($link['status']) ?>
            </div>
            <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px;">Payment link details and performance.</p>
        </div>

        <div style="display: flex; gap: 12px;">
            <button class="btn btn-outline" onclick="copyToClipboard('<?= (getenv('APP_URL') ?: 'http://localhost:8000') ?>/pay/<?= $link['token'] ?>', 'Link URL copied!')">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                Share Link
            </button>
            <a href="/pay/<?= $link['token'] ?>" target="_blank" class="btn btn-primary">
                Open Checkout Page &rarr;
            </a>
        </div>
    </div>

    <!-- URL Box -->
    <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Payment Link</span>
            <code style="font-family: monospace; font-size: 14px; font-weight: 600; color: var(--primary-blue);"><?= (getenv('APP_URL') ?: 'http://localhost:8000') ?>/pay/<?= htmlspecialchars($link['token']) ?></code>
        </div>
        <button class="btn btn-outline btn-sm" onclick="copyToClipboard('<?= (getenv('APP_URL') ?: 'http://localhost:8000') ?>/pay/<?= $link['token'] ?>', 'Copied to clipboard!')">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
        </button>
    </div>

    <!-- Details Grid -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; text-align: left;">
        <div>
            <div style="font-size: 12px; color: var(--text-muted); font-weight: 600;">Amount</div>
            <div style="font-size: 18px; font-weight: 800; color: var(--text-main);">GH₵ <?= number_format($link['amount'], 2) ?></div>
        </div>
        <div>
            <div style="font-size: 12px; color: var(--text-muted); font-weight: 600;">Usage</div>
            <div style="font-size: 18px; font-weight: 800; color: var(--text-main);"><?= number_format($link['usage_count']) ?></div>
        </div>
        <div>
            <div style="font-size: 12px; color: var(--text-muted); font-weight: 600;">Created</div>
            <div style="font-size: 18px; font-weight: 800; color: var(--text-main);"><?= Format::dateShort($link['created_at']) ?></div>
        </div>
    </div>
</div>

<!-- Performance Metrics Section matching mockup -->
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 18px; font-weight: 700;">Performance</h3>
        <select class="select-filter" onchange="showToast('Filtered for ' + this.value)">
            <option value="This Month">This Month</option>
            <option value="All Time">All Time</option>
        </select>
    </div>

    <div class="metrics-grid">
        <div class="card">
            <div class="metric-title" style="margin-bottom: 8px;">Total Views</div>
            <div class="metric-value"><?= number_format($totalViews) ?></div>
        </div>

        <div class="card">
            <div class="metric-title" style="margin-bottom: 8px;">Successful Payments</div>
            <div class="metric-value"><?= number_format($successfulPayments) ?></div>
        </div>

        <div class="card">
            <div class="metric-title" style="margin-bottom: 8px;">Conversion Rate</div>
            <div class="metric-value"><?= $conversionRate ?>%</div>
        </div>

        <div class="card">
            <div class="metric-title" style="margin-bottom: 8px;">Total Volume</div>
            <div class="metric-value">GH₵ <?= number_format($totalVolume, 2) ?></div>
        </div>
    </div>
</div>

<!-- Recent Payments Card matching mockup -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Recent Payments</h3>
        <a href="/transactions" style="font-size: 13px; font-weight: 600;">View all</a>
    </div>

    <div class="transaction-list">
        <?php if (empty($recentPayments)): ?>
            <p style="color: var(--text-muted); text-align: center; padding: 20px;">No payments recorded for this link yet.</p>
        <?php else: foreach ($recentPayments as $pmt): ?>
            <div class="transaction-item">
                <div class="tx-user-info">
                    <div class="tx-avatar">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <div class="tx-name"><?= htmlspecialchars($pmt['customer_name'] ?? 'Guest Customer') ?></div>
                        <div class="tx-date"><?= Format::date($pmt['created_at']) ?></div>
                    </div>
                </div>

                <div class="tx-status-amount">
                    <?= Format::statusBadge($pmt['status']) ?>
                    <div class="tx-amount">GH₵ <?= number_format($pmt['amount'], 2) ?></div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>
