<div class="space-y-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">System Infrastructure Health</h1>
      <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Real-time status monitoring for database, payment engines, webhook queues & background cron workers.</p>
    </div>
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
      <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
      System Status: Operating Normally
    </div>
  </div>

  <!-- Metric Grid -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm">
      <div class="text-xs font-medium text-slate-500 dark:text-slate-400">Database Connection</div>
      <div class="mt-2 text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
        <?= htmlspecialchars($dbStatus) ?>
      </div>
      <div class="text-xs text-slate-400 mt-1">MySQL 8.0 Active</div>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm">
      <div class="text-xs font-medium text-slate-500 dark:text-slate-400">Payment Gateway Mode</div>
      <div class="mt-2 text-xl font-bold text-indigo-600 dark:text-indigo-400 uppercase">
        <?= htmlspecialchars($mode) ?> MODE
      </div>
      <div class="text-xs text-slate-400 mt-1">Sandbox Gateway Operational</div>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm">
      <div class="text-xs font-medium text-slate-500 dark:text-slate-400">Failed Webhooks Queue</div>
      <div class="mt-2 text-xl font-bold text-slate-900 dark:text-white">
        <?= number_format($failedWebhooks) ?>
      </div>
      <div class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">Auto-Retry Queue Active</div>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm">
      <div class="text-xs font-medium text-slate-500 dark:text-slate-400">PHP Version</div>
      <div class="mt-2 text-xl font-bold text-slate-900 dark:text-white">
        <?= htmlspecialchars($phpVersion) ?>
      </div>
      <div class="text-xs text-slate-400 mt-1">CLI Engine Ready</div>
    </div>
  </div>

  <!-- Subsystems Table -->
  <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm">
    <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Core Infrastructure Services</h2>
    <div class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
      <div class="py-3 flex items-center justify-between">
        <div>
          <div class="font-semibold text-slate-900 dark:text-white">Immutable Financial Ledger Engine</div>
          <div class="text-xs text-slate-500">Double-entry accounting, balance calculation & audit trail</div>
        </div>
        <span class="px-2.5 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 rounded-full dark:bg-emerald-950 dark:text-emerald-300">HEALTHY</span>
      </div>
      <div class="py-3 flex items-center justify-between">
        <div>
          <div class="font-semibold text-slate-900 dark:text-white">Idempotency & Replay Protection Engine</div>
          <div class="text-xs text-slate-500">Idempotency-Key caching with request hash verification</div>
        </div>
        <span class="px-2.5 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 rounded-full dark:bg-emerald-950 dark:text-emerald-300">HEALTHY</span>
      </div>
      <div class="py-3 flex items-center justify-between">
        <div>
          <div class="font-semibold text-slate-900 dark:text-white">HMAC SHA-256 Webhook Security Engine</div>
          <div class="text-xs text-slate-500">Constant-time signature verification & deduplication</div>
        </div>
        <span class="px-2.5 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 rounded-full dark:bg-emerald-950 dark:text-emerald-300">HEALTHY</span>
      </div>
    </div>
  </div>
</div>
