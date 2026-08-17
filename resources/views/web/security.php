<section class="py-20 px-4 sm:px-8 glow-bg text-center">
  <div class="max-w-4xl mx-auto space-y-4">
    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-500/10 border border-brand-500/30 text-xs font-mono text-brand-300">
      <span>Security &amp; Integrity</span>
    </div>
    <h1 class="font-display font-extrabold text-4xl sm:text-5xl text-white tracking-tight">Bank-Grade Financial Integrity</h1>
    <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto">Gazoma Pay is engineered with double-entry accounting integrity, strict multi-tenant data isolation, and 256-bit encryption.</p>
  </div>
</section>

<section class="py-16 px-4 sm:px-8 max-w-6xl mx-auto space-y-12">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <div class="glow-card p-8 rounded-3xl space-y-4">
      <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center">
        <span class="material-symbols-outlined text-[28px]">account_balance</span>
      </div>
      <h3 class="font-display font-bold text-2xl text-white">Immutable Double-Entry Ledger</h3>
      <p class="text-slate-400 text-sm leading-relaxed">
        Financial balances are calculated directly from immutable ledger transaction entries. Every customer deposit, platform fee debit, and bank disbursement is recorded as an immutable posting.
      </p>
    </div>

    <div class="glow-card p-8 rounded-3xl space-y-4">
      <div class="w-12 h-12 rounded-2xl bg-brand-500/10 border border-brand-500/30 text-brand-400 flex items-center justify-center">
        <span class="material-symbols-outlined text-[28px]">lock</span>
      </div>
      <h3 class="font-display font-bold text-2xl text-white">256-Bit SSL &amp; PCI-DSS Ready</h3>
      <p class="text-slate-400 text-sm leading-relaxed">
        All network transport is encrypted via TLS 1.3. Card data is processed securely through tokenized gateway endpoints with 3D Secure 2.0 OTP authentication.
      </p>
    </div>

    <div class="glow-card p-8 rounded-3xl space-y-4">
      <div class="w-12 h-12 rounded-2xl bg-accent-500/10 border border-accent-500/30 text-accent-400 flex items-center justify-center">
        <span class="material-symbols-outlined text-[28px]">shield</span>
      </div>
      <h3 class="font-display font-bold text-2xl text-white">Multi-Tenant Merchant Isolation</h3>
      <p class="text-slate-400 text-sm leading-relaxed">
        Every database query is strictly scoped to the authenticated merchant ID (`WHERE merchant_id = ?`). Customer records, keys, and balances are partitioned securely.
      </p>
    </div>

    <div class="glow-card p-8 rounded-3xl space-y-4">
      <div class="w-12 h-12 rounded-2xl bg-purple-500/10 border border-purple-500/30 text-purple-400 flex items-center justify-center">
        <span class="material-symbols-outlined text-[28px]">history</span>
      </div>
      <h3 class="font-display font-bold text-2xl text-white">Audit Trail Logging</h3>
      <p class="text-slate-400 text-sm leading-relaxed">
        Actions across merchant dashboards and platform admin controls are logged in `audit_logs` with timestamp, IP address, user agent, and action metadata.
      </p>
    </div>
  </div>
</section>
