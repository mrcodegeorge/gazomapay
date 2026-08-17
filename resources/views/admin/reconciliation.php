<div class="space-y-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Financial Reconciliation Audit</h1>
      <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Audit logs comparing merchant database balances against double-entry ledger postings.</p>
    </div>
    <form action="/cli/process_reconciliation.php" method="POST" class="inline-block">
      <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
        Run Immediate Audit Scan
      </button>
    </form>
  </div>

  <!-- Audit Runs Table -->
  <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 font-bold text-slate-900 dark:text-white">
      Recent Reconciliation Audit Runs
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-medium">
          <tr>
            <th class="px-6 py-3">Run Code</th>
            <th class="px-6 py-3">Started By</th>
            <th class="px-6 py-3">Ledger Volume</th>
            <th class="px-6 py-3">Discrepancies</th>
            <th class="px-6 py-3">Status</th>
            <th class="px-6 py-3">Timestamp</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php if (empty($runs)): ?>
            <tr>
              <td colspan="6" class="px-6 py-8 text-center text-slate-500">No reconciliation audit runs recorded yet. Execute a scan above to generate audit logs.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($runs as $r): ?>
              <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($r['run_code']) ?></td>
                <td class="px-6 py-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars($r['started_by']) ?></td>
                <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">GH₵ <?= number_format((float)$r['total_ledger_amount'], 2) ?></td>
                <td class="px-6 py-4 font-bold <?= (int)$r['discrepancy_count'] > 0 ? 'text-amber-600' : 'text-emerald-600' ?>">
                  <?= number_format((int)$r['discrepancy_count']) ?>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2.5 py-1 text-xs font-semibold rounded-full <?= $r['status'] === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' ?>">
                    <?= strtoupper(htmlspecialchars($r['status'])) ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-slate-400 text-xs"><?= htmlspecialchars($r['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
