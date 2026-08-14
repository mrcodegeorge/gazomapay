<?php

// Router for PHP built-in web server with robust static asset routing
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Check if request is for a static file in /public/ or root
$possiblePaths = [
    __DIR__ . '/public' . $uri,
    __DIR__ . $uri
];

foreach ($possiblePaths as $filePath) {
    if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimes = [
            'css' => 'text/css; charset=utf-8',
            'js' => 'application/javascript; charset=utf-8',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'ico' => 'image/x-icon'
        ];

        if (isset($mimes[$ext])) {
            header("Content-Type: {$mimes[$ext]}");
        }

        readfile($filePath);
        exit;
    }
}

// Route all application requests to public/index.php
require_once __DIR__ . '/public/index.php';
