<!-- Header Toolbar -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Global Immutable System Audit Trail</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Complete security logging for platform actions, balance adjustments, and operator clearances.</p>
    </div>
</div>

<!-- Search Bar Card -->
<div class="glass-card rounded-xl p-4 border border-outline-variant mb-6 bg-surface">
    <form method="GET" action="/admin/audit-logs" class="w-full md:w-96 relative">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search action code, email, IP, or details..." class="w-full pl-9 pr-4 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-xs text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary">
        <span class="material-symbols-outlined absolute left-3 top-3 text-on-surface-variant text-[16px]">search</span>
    </form>
</div>

<!-- Audit Trail Table Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase">
                    <th class="py-3.5 px-6">Action Code</th>
                    <th class="py-3.5 px-6">User Email</th>
                    <th class="py-3.5 px-6">IP Address</th>
                    <th class="py-3.5 px-6">Details</th>
                    <th class="py-3.5 px-6">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                <?php if (empty($systemLogs)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-12 text-on-surface-variant font-body-sm">No audit logs found matching your query.</td>
                    </tr>
                <?php else: foreach ($systemLogs as $log): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="py-4 px-6 font-data-mono font-bold text-secondary"><?= htmlspecialchars($log['action']) ?></td>
                        <td class="py-4 px-6 font-medium text-on-surface"><?= htmlspecialchars($log['user_email'] ?? 'system@gazomapay.com') ?></td>
                        <td class="py-4 px-6 font-data-mono text-xs text-on-surface-variant"><?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?></td>
                        <td class="py-4 px-6 text-on-surface-variant truncate max-w-md"><?= htmlspecialchars($log['details'] ?? '-') ?></td>
                        <td class="py-4 px-6 text-on-surface-variant font-data-mono text-xs"><?= Format::date($log['created_at']) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
