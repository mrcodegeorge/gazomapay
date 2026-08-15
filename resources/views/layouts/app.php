<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Gazoma Pay') ?> - Merchant Dashboard</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-variant": "#e0e3e5",
                        "secondary-fixed": "#d8e2ff",
                        "on-tertiary-fixed": "#271901",
                        "secondary": "#0058be",
                        "on-primary-fixed": "#131b2e",
                        "on-secondary": "#ffffff",
                        "secondary-container": "#2170e4",
                        "primary-fixed-dim": "#bec6e0",
                        "surface-container-highest": "#e0e3e5",
                        "tertiary": "#000000",
                        "primary-fixed": "#dae2fd",
                        "inverse-on-surface": "#eff1f3",
                        "surface-container-high": "#e6e8ea",
                        "inverse-primary": "#bec6e0",
                        "secondary-fixed-dim": "#adc6ff",
                        "background": "#f7f9fb",
                        "on-secondary-fixed-variant": "#004395",
                        "outline-variant": "#c6c6cd",
                        "surface-container-low": "#f2f4f6",
                        "surface-bright": "#f7f9fb",
                        "on-surface-variant": "#45464d",
                        "error": "#ba1a1a",
                        "on-tertiary-container": "#98805d",
                        "on-tertiary": "#ffffff",
                        "surface": "#f7f9fb",
                        "surface-tint": "#565e74",
                        "surface-container": "#eceef0",
                        "error-container": "#ffdad6",
                        "on-secondary-container": "#fefcff",
                        "inverse-surface": "#2d3133",
                        "tertiary-fixed": "#fcdeb5",
                        "on-primary": "#ffffff",
                        "on-surface": "#191c1e",
                        "outline": "#76777d",
                        "on-error": "#ffffff",
                        "on-background": "#191c1e",
                        "on-primary-container": "#7c839b",
                        "on-tertiary-fixed-variant": "#574425",
                        "on-secondary-fixed": "#001a42",
                        "surface-dim": "#d8dadc",
                        "primary": "#000000",
                        "primary-container": "#131b2e",
                        "tertiary-fixed-dim": "#dec29a",
                        "tertiary-container": "#271901",
                        "on-error-container": "#93000a",
                        "surface-container-lowest": "#ffffff",
                        "on-primary-fixed-variant": "#3f465c"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "margin-desktop": "40px",
                        "margin-mobile": "16px",
                        "gutter": "24px",
                        "unit": "8px",
                        "container-max": "1280px"
                    },
                    "fontFamily": {
                        "headline-lg": ["Geist"],
                        "body-lg": ["Inter"],
                        "label-caps": ["Inter"],
                        "display": ["Geist"],
                        "body-sm": ["Inter"],
                        "headline-md": ["Geist"],
                        "data-mono": ["JetBrains Mono"],
                        "headline-lg-mobile": ["Geist"],
                        "body-md": ["Inter"]
                    },
                    "fontSize": {
                        "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
                        "label-caps": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600" }],
                        "display": ["48px", { "lineHeight": "1.1", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "body-sm": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                        "headline-md": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
                        "data-mono": ["14px", { "lineHeight": "20px", "fontWeight": "500" }],
                        "headline-lg-mobile": ["28px", { "lineHeight": "36px", "fontWeight": "600" }],
                        "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 20px;
            line-height: 1;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-smoothing: antialiased;
        }
        .glass-card {
            background: #ffffff;
            border: 1px solid #c6c6cd;
            transition: box-shadow 0.2s ease-out;
        }
        .glass-card:hover {
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.08);
        }
    </style>
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
