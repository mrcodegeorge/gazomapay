<?php
$user = Auth::user();
$userName = htmlspecialchars($user['name'] ?? 'Super Admin');
$userEmail = htmlspecialchars($user['email'] ?? 'admin@gazomapay.com');
?>
<header class="h-16 bg-slate-950/80 backdrop-blur-md border-b border-slate-800 fixed top-0 right-0 left-[260px] z-40 px-6 flex items-center justify-between font-body text-slate-100">
    
    <!-- Title & System Status Badge -->
    <div class="flex items-center gap-3">
        <span class="font-headline font-bold text-sm text-white">Super Admin Command Console</span>
        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-data-mono text-[10px] font-bold flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Live System Core
        </span>
    </div>

    <!-- Right Controls: Operator Profile -->
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-3 pl-4 border-l border-slate-800">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-500 to-rose-600 text-white flex items-center justify-center font-bold text-xs shadow-md">
                SA
            </div>
            <div class="hidden sm:block text-left">
                <div class="font-body text-xs font-bold text-white"><?= $userName ?></div>
                <div class="font-data-mono text-[10px] text-amber-400 font-bold uppercase tracking-wider">Superadmin Operator</div>
            </div>
        </div>
    </div>
</header>
