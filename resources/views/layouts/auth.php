<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Authentication') ?> - Gazoma Pay</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        body { background: #0b132b; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        .auth-card { background: #ffffff; border-radius: 16px; padding: 40px; width: 100%; max-width: 440px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .auth-brand { text-align: center; margin-bottom: 28px; }
        .auth-brand h2 { font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-brand">
            <div class="brand-icon" style="margin: 0 auto; width: 44px; height: 44px; font-size: 22px;">G</div>
            <h2>Gazoma Pay</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">Fintech Infrastructure Platform</p>
        </div>

        <?php if ($flashError = Response::getFlash('error')): ?>
            <div class="badge badge-danger" style="padding: 10px; margin-bottom: 16px; width: 100%; border-radius: 6px; text-align: center;">
                <?= htmlspecialchars($flashError) ?>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </div>
</body>
</html>
