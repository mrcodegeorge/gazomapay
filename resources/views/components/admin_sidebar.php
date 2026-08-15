<?php
$currentRoute = $_SERVER['REQUEST_URI'] ?? '/admin';
$user = Auth::user();

function isAdminNavActive(string $path, string $currentRoute): string {
    if ($path === '/admin' && ($currentRoute === '/admin' || $currentRoute === '/admin/')) {
        return 'bg-surface-container-high text-primary font-bold border-r-2 border-primary';
    }
    return (strpos($currentRoute, $path) === 0 && $path !== '/admin') ? 'bg-surface-container-high text-primary font-bold border-r-2 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high';
}
?>
<aside class="w-[260px] h-screen fixed left-0 top-0 bg-surface border-r border-outline-variant flex flex-col py-6 px-4 z-50 select-none">
    <!-- Brand Header -->
    <div class="flex items-center gap-3 mb-8 px-2">
        <a href="/admin" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary text-on-primary flex items-center justify-center font-bold text-xl shadow-sm">G</div>
            <div>
                <h1 class="font-headline-md text-headline-md font-bold text-on-surface leading-none mb-1">Gazoma Pay</h1>
                <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary font-data-mono text-[9px] font-bold uppercase tracking-wider">Super Admin</span>
            </div>
        </a>
    </div>

    <!-- Standalone Admin Navigation List -->
    <nav class="flex-1 overflow-y-auto space-y-1">
        <div class="px-3 pb-2 font-label-caps text-[10px] font-bold text-on-surface-variant/70 uppercase tracking-wider">Platform Command</div>

        <a href="/admin" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200 <?= isAdminNavActive('/admin', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">space_dashboard</span>
            <span>Platform Command</span>
        </a>

        <a href="/admin#merchants" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200">
            <span class="material-symbols-outlined text-[20px]">store</span>
            <span>Merchants & KYB</span>
        </a>

        <a href="/admin#settlements" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200">
            <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
            <span>Settlement Clearance</span>
        </a>

        <a href="/admin#disputes" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200">
            <span class="material-symbols-outlined text-[20px]">gavel</span>
            <span>Platform Disputes</span>
        </a>

        <div class="px-3 pt-6 pb-2 font-label-caps text-[10px] font-bold text-on-surface-variant/70 uppercase tracking-wider">System Governance</div>

        <a href="/admin#settings" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200">
            <span class="material-symbols-outlined text-[20px]">settings_suggest</span>
            <span>Gateway Settings</span>
        </a>

        <a href="/admin#audit" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200">
            <span class="material-symbols-outlined text-[20px]">verified_user</span>
            <span>System Audit Trail</span>
        </a>

        <a href="/developer" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200">
            <span class="material-symbols-outlined text-[20px]">code</span>
            <span>Developer Portal</span>
        </a>
    </nav>

    <!-- Merchant Mode Switcher Footer Button -->
    <div class="mt-auto pt-4 border-t border-outline-variant">
        <a href="/dashboard" class="p-3 bg-surface-container-low hover:bg-surface-container-high rounded-xl border border-outline-variant flex items-center justify-between transition-colors group">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary text-[18px]">storefront</span>
                <span class="font-body-sm text-xs font-bold text-on-surface group-hover:text-primary">Exit to Merchant</span>
            </div>
            <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary text-[16px]">arrow_forward</span>
        </a>
    </div>
</aside>
