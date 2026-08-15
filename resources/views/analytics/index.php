<!-- 4 Bento Metric Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Gross Revenue</span>
            <span class="material-symbols-outlined text-secondary">payments</span>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg text-on-surface font-bold">GH₵ <?= number_format($gross, 2) ?></div>
            <div class="flex items-center gap-1 font-body-sm text-body-sm text-[#10B981] mt-1">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>+12.5% vs last month</span>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Platform Fees</span>
            <span class="material-symbols-outlined text-on-surface-variant">receipt</span>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg text-on-surface-variant font-bold">GH₵ <?= number_format($fee, 2) ?></div>
            <span class="font-body-sm text-body-sm text-on-surface-variant">Average fee 1.5% + GH₵ 0.50</span>
        </div>
    </div>

    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36 border-l-4 border-l-[#10B981]">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Net Revenue</span>
            <span class="material-symbols-outlined text-[#10B981]">verified</span>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg text-[#10B981] font-bold">GH₵ <?= number_format($net, 2) ?></div>
            <span class="font-body-sm text-body-sm text-on-surface-variant">Net earnings deposited to ledger</span>
        </div>
    </div>

    <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36">
        <div class="flex items-center justify-between">
            <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Avg Transaction</span>
            <span class="material-symbols-outlined text-secondary">analytics</span>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg text-secondary font-bold font-data-mono">GH₵ <?= number_format($avgTx, 2) ?></div>
            <span class="font-body-sm text-body-sm text-on-surface-variant">Per successful order</span>
        </div>
    </div>
</div>

<!-- Chart Container Card -->
<div class="glass-card rounded-xl p-6 border border-outline-variant mb-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-semibold">Revenue & Volume Growth Trend</h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Historical gross processing volume trajectories</p>
        </div>
        <select class="px-3 py-1.5 bg-surface-container-low border border-outline-variant rounded-lg font-body-sm text-body-sm text-on-surface focus:outline-none">
            <option value="This Year">This Year (2024)</option>
            <option value="Last 12 Months">Last 12 Months</option>
        </select>
    </div>
    <div class="h-80 w-full relative">
        <canvas id="analyticsChart"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    initRevenueChart('analyticsChart', ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'], [45000, 62000, 78000, 95000, 126560, 142000, 168000]);
});
</script>
