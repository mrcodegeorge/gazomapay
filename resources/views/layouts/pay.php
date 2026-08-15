<!DOCTYPE html>
<html lang="en" class="h-full bg-background">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Secure Checkout') ?> - Gazoma Pay</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#000000",
                        "on-primary": "#ffffff",
                        "secondary": "#0058be",
                        "secondary-container": "#2170e4",
                        "background": "#f7f9fb",
                        "surface": "#ffffff",
                        "surface-container-low": "#f2f4f6",
                        "on-surface": "#191c1e",
                        "on-surface-variant": "#45464d",
                        "outline-variant": "#c6c6cd"
                    },
                    fontFamily: {
                        "headline-lg": ["Geist", "sans-serif"],
                        "body-md": ["Inter", "sans-serif"],
                        "data-mono": ["JetBrains Mono", "monospace"]
                    }
                }
            }
        }
    </script>
    <style>
        .glass-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.01);
        }
    </style>
</head>
<body class="font-body-md text-body-md text-on-surface bg-[#f8fafc] min-h-screen flex flex-col antialiased selection:bg-secondary selection:text-white">
    <?= $content ?>
    <script src="/assets/js/app.js"></script>
</body>
</html>
