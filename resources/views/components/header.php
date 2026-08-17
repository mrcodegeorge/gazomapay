<?php
$user = Auth::user();
$userName = htmlspecialchars($user['name'] ?? 'John Mensah');
$userRole = ucfirst(htmlspecialchars($user['role'] ?? 'Admin'));
$env = $user['environment'] ?? 'live';

// Unread notifications count
$pdo = Database::getConnection();
$mId = Auth::merchantId();
$stmtN = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE merchant_id = ? AND is_read = 0");
$stmtN->execute([$mId]);
$unreadCount = (int)$stmtN->fetchColumn();
?>
<header class="h-16 sticky top-0 z-40 bg-surface/90 backdrop-blur-md border-b border-outline-variant flex justify-between items-center w-full px-8 ml-[260px] max-w-[calc(100%-260px)]">
    <!-- Left Section: Title & Subtitle -->
    <div class="flex items-center gap-4">
        <div>
            <h1 class="font-headline-md text-headline-md font-bold text-on-surface leading-tight"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
            <?php if (!empty($pageSubtitle)): ?>
                <p class="font-body-sm text-body-sm text-on-surface-variant hidden md:block"><?= htmlspecialchars($pageSubtitle) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Section: Actions & User Avatar -->
    <div class="flex items-center gap-5">
        <!-- Live/Test Mode Interactive Toggle Switcher -->
        <form action="/settings/toggle-mode" method="POST" class="inline-block m-0 p-0">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <button type="submit" title="Click to switch environment mode" class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container-low border border-outline-variant/60 text-body-sm font-medium hover:bg-surface-container-high transition-all cursor-pointer group">
                <span class="w-2.5 h-2.5 rounded-full <?= $env === 'test' ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500 animate-pulse' ?>"></span>
                <span class="font-label-caps text-label-caps uppercase font-bold text-on-surface group-hover:text-primary transition-colors"><?= $env === 'test' ? 'Test Mode' : 'Live Mode' ?></span>
                <span class="material-symbols-outlined text-[16px] text-on-surface-variant group-hover:rotate-180 transition-transform">sync</span>
            </button>
        </form>

        <!-- Notifications Bell -->
        <button class="relative hover:bg-surface-container-low p-2 rounded-full transition-colors flex items-center justify-center text-on-surface-variant cursor-pointer" onclick="openModal('notificationsModal')" title="Notifications">
            <span class="material-symbols-outlined text-[22px]">notifications</span>
            <?php if ($unreadCount > 0): ?>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-secondary ring-2 ring-surface"></span>
            <?php endif; ?>
        </button>

        <!-- User Profile Dropdown Button -->
        <a href="/settings/profile" class="flex items-center gap-3 hover:bg-surface-container-low p-1.5 px-2.5 rounded-full border border-outline-variant/60 transition-colors">
            <div class="w-7 h-7 rounded-full bg-surface-variant flex items-center justify-center font-bold text-xs text-on-surface overflow-hidden">
                <span class="material-symbols-outlined text-[18px]">person</span>
            </div>
            <div class="hidden sm:block text-left pr-1">
                <div class="font-body-sm text-body-sm font-semibold text-on-surface leading-none mb-0.5"><?= $userName ?></div>
                <div class="font-label-caps text-[10px] text-on-surface-variant leading-none uppercase"><?= $userRole ?></div>
            </div>
        </a>
    </div>
</header>
