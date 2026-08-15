<?php
$currentRoute = $_SERVER['REQUEST_URI'] ?? '/admin';
$user = Auth::user();

function isAdminNavActive(string $path, string $currentRoute): string {
    if ($path === '/admin' && ($currentRoute === '/admin' || $currentRoute === '/admin/')) {
        return 'bg-slate-800 text-amber-400 font-bold border-r-2 border-amber-400';
    }
    return (strpos($currentRoute, $path) === 0 && $path !== '/admin') ? 'bg-slate-800 text-amber-400 font-bold border-r-2 border-amber-400' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200';
}
?>
<aside class="w-[260px] h-screen fixed left-0 top-0 bg-slate-950 border-r border-slate-800 flex flex-col py-6 px-4 z-50 select-none font-body text-slate-100">
    <!-- Brand Header -->
    <div class="flex items-center gap-3 mb-8 px-2">
        <a href="/admin" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-rose-600 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-rose-500/20">
                <span class="material-symbols-outlined text-[22px]">shield_person</span>
            </div>
            <div>
                <h1 class="font-headline text-base font-black text-white leading-none mb-1 flex items-center gap-2">
                    <span>Gazoma Pay</span>
                </h1>
                <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 font-data-mono text-[9px] font-extrabold uppercase tracking-wider">Super Admin</span>
            </div>
        </a>
    </div>

    <!-- Admin Navigation List -->
    <nav class="flex-1 overflow-y-auto space-y-1">
        <div class="px-3 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Platform Command</div>

        <a href="/admin" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-body-sm text-sm transition-all duration-200 <?= isAdminNavActive('/admin', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">space_dashboard</span>
            <span>Platform Overview</span>
        </a>

        <a href="/admin#merchants" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-body-sm text-sm transition-all duration-200 <?= isAdminNavActive('/admin/merchants', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">store</span>
            <span>Merchant KYB</span>
        </a>

        <a href="/admin#settlements" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-body-sm text-sm transition-all duration-200 <?= isAdminNavActive('/admin/settlements', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
            <span>Settlement Payouts</span>
        </a>

        <a href="/disputes" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-body-sm text-sm transition-all duration-200 <?= isAdminNavActive('/disputes', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">gavel</span>
            <span>Platform Disputes</span>
        </a>

        <div class="px-3 pt-6 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">System Governance</div>

        <a href="/admin#audit" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-body-sm text-sm transition-all duration-200">
            <span class="material-symbols-outlined text-[20px]">verified_user</span>
            <span>Global Audit Logs</span>
        </a>

        <a href="/developer" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-body-sm text-sm transition-all duration-200">
            <span class="material-symbols-outlined text-[20px]">code</span>
            <span>Developer Portal</span>
        </a>
    </nav>

    <!-- Return to Merchant View Footer Button -->
    <div class="mt-auto pt-4 border-t border-slate-800">
        <a href="/dashboard" class="p-3 bg-slate-900 hover:bg-slate-800 rounded-xl border border-slate-800 flex items-center justify-between transition-colors group">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-slate-400 group-hover:text-white text-[18px]">storefront</span>
                <span class="font-body-sm text-xs font-bold text-slate-300 group-hover:text-white">Merchant View</span>
            </div>
            <span class="material-symbols-outlined text-slate-500 group-hover:text-white text-[16px]">arrow_forward</span>
        </a>
    </div>
</aside>
