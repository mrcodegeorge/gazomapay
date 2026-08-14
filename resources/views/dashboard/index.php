<!-- Top Header Action Bar -->
<div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 24px;">
    <a href="/analytics/report-csv" class="btn btn-outline">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Download reports
    </a>
    <button class="btn btn-outline" onclick="showToast('Filter options applied')">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
        Filter
    </button>
</div>

<!-- 4 Top Metric Cards matching Mockup -->
<div class="metrics-grid">
    <!-- Total Volume -->
    <div class="card">
        <div class="metric-card-header">
            <span class="metric-title">Total Volume</span>
            <div class="metric-icon-box blue">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 012 2h2a2 2 0 012-2z"/></svg>
            </div>
        </div>
        <div class="metric-value">GH₵ <?= number_format($totalVolume, 2) ?></div>
        <div class="metric-subtext">
            <span class="metric-change up">↑ 12.5%</span> vs last month
        </div>
    </div>

    <!-- Successful Transactions -->
    <div class="card">
        <div class="metric-card-header">
            <span class="metric-title">Successful Transactions</span>
            <div class="metric-icon-box green">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>
        <div class="metric-value"><?= number_format($successfulTxCount) ?></div>
        <div class="metric-subtext">
            <span class="metric-change up">↑ 8.7%</span> vs last month
        </div>
    </div>

    <!-- Customers -->
    <div class="card">
        <div class="metric-card-header">
            <span class="metric-title">Customers</span>
            <div class="metric-icon-box purple">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
        <div class="metric-value"><?= number_format($totalCustomers) ?></div>
        <div class="metric-subtext">
            <span class="metric-change up">↑ 10.3%</span> vs last month
        </div>
    </div>

    <!-- Available Balance -->
    <div class="card">
        <div class="metric-card-header">
            <span class="metric-title">Available Balance</span>
            <div class="metric-icon-box outline-blue">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
        </div>
        <div class="metric-value">GH₵ <?= number_format($availableBalance, 2) ?></div>
        <a href="javascript:void(0)" onclick="openModal('withdrawModal')" class="withdraw-btn">Withdraw</a>
    </div>
</div>

<!-- Dashboard Section: Analytics Overview & Recent Transactions -->
<div class="dashboard-grid">
    <!-- Interactive Line Chart Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Overview</h3>
            <select class="select-filter" onchange="showToast('Chart updated for ' + this.value)">
                <option value="This Month">This Month</option>
                <option value="Today">Today</option>
                <option value="7 Days">7 Days</option>
                <option value="30 Days">30 Days</option>
                <option value="This Year">This Year</option>
            </select>
        </div>
        <div style="height: 280px; position: relative;">
            <canvas id="overviewChart"></canvas>
        </div>
    </div>

    <!-- Recent Transactions List Card matching mockup -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Transactions</h3>
            <a href="/transactions" style="font-size: 13px; font-weight: 600;">View all</a>
        </div>
        
        <div class="transaction-list">
            <?php foreach ($recentTransactions as $tx): ?>
                <div class="transaction-item">
                    <div class="tx-user-info">
                        <div class="tx-avatar">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <div class="tx-name"><?= htmlspecialchars($tx['customer_name'] ?? 'Guest Customer') ?></div>
                            <div class="tx-date"><?= Format::date($tx['created_at']) ?></div>
                        </div>
                    </div>
                    
                    <div class="tx-status-amount">
                        <?= Format::statusBadge($tx['status']) ?>
                        <div class="tx-amount">GH₵ <?= number_format($tx['amount'], 2) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal-overlay" id="withdrawModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Withdraw Available Balance</h3>
            <button class="modal-close" onclick="closeModal('withdrawModal')">&times;</button>
        </div>
        <form action="/settlements/request" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group">
                <label class="form-label">Available Balance</label>
                <input type="text" class="form-control" value="GH₵ <?= number_format($availableBalance, 2) ?>" readonly style="background: #f8fafc; font-weight: 700;">
            </div>
            <div class="form-group">
                <label class="form-label">Withdrawal Amount (GHS)</label>
                <input type="number" step="0.01" max="<?= $availableBalance ?>" name="amount" class="form-control" value="<?= $availableBalance ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Bank Account</label>
                <select name="bank_name" class="form-control" required>
                    <option value="GCB Bank Ghana - 1011129384728">GCB Bank Ghana (1011129384728)</option>
                    <option value="Stanbic Bank Ghana - 9040001827364">Stanbic Bank Ghana (9040001827364)</option>
                    <option value="Ecobank Ghana - 4401928374651">Ecobank Ghana (4401928374651)</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('withdrawModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Confirm Withdrawal</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    initRevenueChart('overviewChart', <?= $chartLabels ?>, <?= $chartData ?>);
});
</script>
