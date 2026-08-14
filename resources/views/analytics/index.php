<div class="metrics-grid">
    <div class="card">
        <div class="metric-title" style="margin-bottom: 8px;">Gross Revenue</div>
        <div class="metric-value">GH₵ <?= number_format($gross, 2) ?></div>
        <div class="metric-subtext"><span class="metric-change up">↑ 12.5%</span> vs last month</div>
    </div>
    <div class="card">
        <div class="metric-title" style="margin-bottom: 8px;">Platform Fees</div>
        <div class="metric-value" style="color: #64748b;">GH₵ <?= number_format($fee, 2) ?></div>
        <div class="metric-subtext">Average fee 1.5%</div>
    </div>
    <div class="card">
        <div class="metric-title" style="margin-bottom: 8px;">Net Revenue</div>
        <div class="metric-value" style="color: #16a34a;">GH₵ <?= number_format($net, 2) ?></div>
        <div class="metric-subtext">Net earnings deposited</div>
    </div>
    <div class="card">
        <div class="metric-title" style="margin-bottom: 8px;">Average Transaction</div>
        <div class="metric-value" style="color: var(--primary-blue);">GH₵ <?= number_format($avgTx, 2) ?></div>
        <div class="metric-subtext">Per successful order</div>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">Revenue & Volume Growth Trend</h3>
        <select class="select-filter">
            <option value="This Year">This Year (2024)</option>
            <option value="Last 12 Months">Last 12 Months</option>
        </select>
    </div>
    <div style="height: 300px; position: relative;">
        <canvas id="analyticsChart"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    initRevenueChart('analyticsChart', ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'], [45000, 62000, 78000, 95000, 126560, 142000, 168000]);
});
</script>
