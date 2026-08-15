<!DOCTYPE html>
<html class="h-full bg-background dark:bg-inverse-surface" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= htmlspecialchars($pageTitle ?? 'Authentication') ?> - Gazoma Pay</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
        }
    </style>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-background": "#191c1e",
                        "on-primary-fixed": "#131b2e",
                        "background": "#f7f9fb",
                        "secondary-fixed-dim": "#adc6ff",
                        "surface-tint": "#565e74",
                        "on-tertiary-fixed-variant": "#574425",
                        "error-container": "#ffdad6",
                        "secondary": "#0058be",
                        "outline": "#76777d",
                        "inverse-on-surface": "#eff1f3",
                        "on-surface": "#191c1e",
                        "surface-variant": "#e0e3e5",
                        "primary-fixed": "#dae2fd",
                        "on-primary-container": "#7c839b",
                        "inverse-surface": "#2d3133",
                        "error": "#ba1a1a",
                        "on-error-container": "#93000a",
                        "on-primary": "#ffffff",
                        "surface-dim": "#d8dadc",
                        "tertiary-container": "#271901",
                        "surface": "#f7f9fb",
                        "on-tertiary": "#ffffff",
                        "on-secondary": "#ffffff",
                        "surface-container-high": "#e6e8ea",
                        "on-primary-fixed-variant": "#3f465c",
                        "primary-container": "#131b2e",
                        "on-tertiary-fixed": "#271901",
                        "on-surface-variant": "#45464d",
                        "on-secondary-fixed": "#001a42",
                        "tertiary-fixed-dim": "#dec29a",
                        "surface-container-lowest": "#ffffff",
                        "primary": "#000000",
                        "primary-fixed-dim": "#bec6e0",
                        "outline-variant": "#c6c6cd",
                        "on-secondary-container": "#fefcff",
                        "secondary-fixed": "#d8e2ff",
                        "surface-container-highest": "#e0e3e5",
                        "on-secondary-fixed-variant": "#004395",
                        "surface-bright": "#f7f9fb",
                        "secondary-container": "#2170e4",
                        "surface-container-low": "#f2f4f6",
                        "on-error": "#ffffff",
                        "surface-container": "#eceef0",
                        "inverse-primary": "#bec6e0",
                        "tertiary-fixed": "#fcdeb5",
                        "tertiary": "#000000",
                        "on-tertiary-container": "#98805d"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "body-lg": ["Inter"],
                        "headline-lg-mobile": ["Geist"],
                        "display": ["Geist"],
                        "body-md": ["Inter"],
                        "label-caps": ["Inter"],
                        "headline-lg": ["Geist"],
                        "headline-md": ["Geist"],
                        "body-sm": ["Inter"],
                        "data-mono": ["JetBrains Mono"]
                    },
                    "fontSize": {
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "600"}],
                        "display": ["48px", {"lineHeight": "1.1", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "data-mono": ["14px", {"lineHeight": "20px", "fontWeight": "500"}]
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex items-center justify-center p-4 min-h-screen">
    <?= $content ?>
</body>
</html>
