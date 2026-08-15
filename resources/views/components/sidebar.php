<?php
$currentRoute = $_SERVER['REQUEST_URI'] ?? '/dashboard';
$user = Auth::user();
$merchantName = htmlspecialchars($user['merchant_name'] ?? 'Gazoma Tech');
$merchantCode = htmlspecialchars($user['merchant_code'] ?? 'GZM_123456');

function isNavActive(string $path, string $currentRoute): string {
    if ($path === '/dashboard' && ($currentRoute === '/' || $currentRoute === '/dashboard')) {
        return 'bg-surface-container-high text-secondary dark:text-secondary-fixed-dim font-bold border-r-2 border-secondary dark:border-secondary-fixed-dim';
    }
    return (strpos($currentRoute, $path) === 0 && $path !== '/dashboard') ? 'bg-surface-container-high text-secondary dark:text-secondary-fixed-dim font-bold border-r-2 border-secondary dark:border-secondary-fixed-dim' : 'text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high';
}
?>
<aside class="w-[260px] h-screen fixed left-0 top-0 bg-surface dark:bg-inverse-surface border-r border-outline-variant dark:border-outline flex flex-col py-6 px-4 z-50 select-none">
    <!-- Brand Header -->
    <div class="flex items-center gap-3 mb-8 px-2">
        <a href="/dashboard" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary text-on-primary flex items-center justify-center font-bold text-xl shadow-sm">G</div>
            <div>
                <h1 class="font-headline-md text-headline-md font-bold text-on-surface dark:text-inverse-on-surface leading-none mb-0.5">Gazoma Pay</h1>
                <p class="font-label-caps text-[11px] text-on-surface-variant uppercase tracking-wider">Fintech Infrastructure</p>
            </div>
        </a>
    </div>

    <!-- Navigation List -->
    <nav class="flex-1 overflow-y-auto space-y-1">
        <a href="/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200 <?= isNavActive('/dashboard', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span>Dashboard</span>
        </a>

        <a href="/transactions" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200 <?= isNavActive('/transactions', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">receipt_long</span>
            <span>Transactions</span>
        </a>

        <a href="/customers" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200 <?= isNavActive('/customers', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">group</span>
            <span>Customers</span>
        </a>

        <a href="/settlements" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200 <?= isNavActive('/settlements', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
            <span>Settlements</span>
        </a>

        <a href="/disputes" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200 <?= isNavActive('/disputes', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">gavel</span>
            <span>Disputes</span>
        </a>

        <a href="/payment-links" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200 <?= isNavActive('/payment-links', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">link</span>
            <span>Payment Links</span>
        </a>

        <a href="/invoices" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200 <?= isNavActive('/invoices', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">description</span>
            <span>Invoices</span>
        </a>

        <a href="/subscriptions" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200 <?= isNavActive('/subscriptions', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">receipt</span>
            <span>Subscriptions</span>
        </a>

        <a href="/analytics" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200 <?= isNavActive('/analytics', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">analytics</span>
            <span>Analytics</span>
        </a>

        <a href="/developer" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200 <?= isNavActive('/developer', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">code</span>
            <span>Developer</span>
        </a>

        <a href="/settings" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-body-md text-body-md transition-all duration-200 <?= isNavActive('/settings', $currentRoute) ?>">
            <span class="material-symbols-outlined text-[20px]">settings</span>
            <span>Settings</span>
        </a>
    </nav>

    <!-- Merchant Footer Card -->
    <div class="mt-auto pt-4 border-t border-outline-variant">
        <div class="p-3 bg-surface-container-low rounded-xl border border-outline-variant/60 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-primary text-on-primary flex items-center justify-center font-bold text-sm">G</div>
            <div class="flex-1 min-w-0">
                <div class="font-body-sm text-body-sm font-semibold text-on-surface truncate"><?= $merchantName ?></div>
                <div class="font-label-caps text-[11px] text-on-surface-variant truncate"><?= $merchantCode ?></div>
            </div>
            <a href="/logout" title="Sign out" class="text-on-surface-variant hover:text-error transition-colors p-1 rounded">
                <span class="material-symbols-outlined text-lg">logout</span>
            </a>
        </div>
    </div>
</aside>
