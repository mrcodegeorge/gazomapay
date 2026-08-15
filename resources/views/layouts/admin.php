<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Super Admin Console') ?> | Gazoma Pay</title>

    <!-- Tailwind CSS CDN & Google Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="/assets/css/app.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-data-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-surface-container-lowest text-on-surface antialiased min-h-screen">

    <!-- Standalone Admin Sidebar -->
    <?php require __DIR__ . '/../components/admin_sidebar.php'; ?>

    <!-- Main Standalone Layout Wrapper -->
    <div class="pl-[260px] flex flex-col min-h-screen">
        
        <!-- Standalone Admin Header -->
        <?php require __DIR__ . '/../components/admin_header.php'; ?>

        <!-- Content Area -->
        <main class="flex-1 pt-20 pb-12 px-8 max-w-7xl w-full mx-auto">
            <?= $content ?>
        </main>
    </div>

    <!-- Application JavaScript -->
    <script src="/assets/js/app.js"></script>
</body>
</html>
