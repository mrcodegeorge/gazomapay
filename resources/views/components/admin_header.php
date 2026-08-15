<?php
$user = Auth::user();
$userName = htmlspecialchars($user['name'] ?? 'Super Admin');
$userEmail = htmlspecialchars($user['email'] ?? 'admin@gazomapay.com');
?>
<header class="h-16 bg-surface/80 backdrop-blur-md border-b border-outline-variant fixed top-0 right-0 left-[260px] z-40 px-6 flex items-center justify-between">
    <!-- Title & System Status Badge -->
    <div class="flex items-center gap-3">
        <h2 class="font-headline-md text-sm font-bold text-on-surface">Super Admin Console</h2>
        <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-data-mono text-[10px] font-bold border border-emerald-200 flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Platform Core Operational
        </span>
    </div>

    <!-- Right Controls: Operator Profile -->
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-3 pl-4 border-l border-outline-variant">
            <div class="w-8 h-8 rounded-lg bg-primary text-on-primary flex items-center justify-center font-bold text-xs shadow-sm">
                SA
            </div>
            <div class="hidden sm:block text-left">
                <div class="font-body-sm text-xs font-bold text-on-surface"><?= $userName ?></div>
                <div class="font-data-mono text-[10px] text-secondary font-bold uppercase tracking-wider">Superadmin Operator</div>
            </div>
        </div>
    </div>
</header>
