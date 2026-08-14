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
<header class="top-header">
    <div class="page-title-group">
        <h1><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
        <?php if (!empty($pageSubtitle)): ?>
            <p><?= htmlspecialchars($pageSubtitle) ?></p>
        <?php endif; ?>
    </div>

    <div class="header-actions">
        <!-- Live/Test Mode Indicator -->
        <div class="mode-badge" id="modeSwitchBtn" title="Switch environment mode">
            <span class="mode-dot <?= $env === 'test' ? 'test' : '' ?>"></span>
            <span><?= $env === 'test' ? 'Test mode' : 'Live mode' ?></span>
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>

        <!-- Notifications Bell -->
        <button class="icon-btn" onclick="openModal('notificationsModal')" title="Notifications">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <?php if ($unreadCount > 0): ?>
                <span class="notification-dot"></span>
            <?php endif; ?>
        </button>

        <!-- User Profile Dropdown -->
        <div class="user-profile-btn" onclick="location.href='/settings/profile'">
            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80" alt="Avatar" class="user-avatar">
            <div class="user-info">
                <span class="user-name"><?= $userName ?></span>
                <span class="user-role"><?= $userRole ?></span>
            </div>
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-muted);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
    </div>
</header>
