<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Gazoma Pay') ?> - Merchant Dashboard</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Styles -->
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <div class="app-container">
        <?php require __DIR__ . '/../components/sidebar.php'; ?>
        
        <div class="main-content">
            <?php require __DIR__ . '/../components/header.php'; ?>
            
            <main class="page-body">
                <?php if ($flashSuccess = Response::getFlash('success')): ?>
                    <div class="badge badge-success" style="padding: 12px 18px; margin-bottom: 20px; width: 100%; border-radius: 8px; font-size: 14px;">
                        ✓ <?= htmlspecialchars($flashSuccess) ?>
                    </div>
                <?php endif; ?>

                <?php if ($flashError = Response::getFlash('error')): ?>
                    <div class="badge badge-danger" style="padding: 12px 18px; margin-bottom: 20px; width: 100%; border-radius: 8px; font-size: 14px;">
                        ✕ <?= htmlspecialchars($flashError) ?>
                    </div>
                <?php endif; ?>

                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Notifications Modal -->
    <div class="modal-overlay" id="notificationsModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title">Notifications</h3>
                <button class="modal-close" onclick="closeModal('notificationsModal')">&times;</button>
            </div>
            <div style="display: flex; flex-direction: column; gap: 12px; max-height: 360px; overflow-y: auto;">
                <?php
                $pdo = Database::getConnection();
                $mId = Auth::merchantId();
                $stmtN = $pdo->prepare("SELECT * FROM notifications WHERE merchant_id = ? ORDER BY created_at DESC LIMIT 10");
                $stmtN->execute([$mId]);
                $notifs = $stmtN->fetchAll();
                if (empty($notifs)):
                ?>
                    <p style="color: var(--text-muted); text-align: center; padding: 20px;">No new notifications</p>
                <?php else: foreach ($notifs as $n): ?>
                    <div style="padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; background: #f8fafc;">
                        <div style="font-weight: 700; font-size: 13px; margin-bottom: 2px; color: var(--text-main);"><?= htmlspecialchars($n['title']) ?></div>
                        <div style="font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars($n['message']) ?></div>
                        <div style="font-size: 10px; color: var(--text-light); margin-top: 4px;"><?= Format::date($n['created_at']) ?></div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/charts.js"></script>
</body>
</html>
