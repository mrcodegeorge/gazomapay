<?php

// Load .env if exists
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv("{$name}={$value}");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

return [
    'name' => getenv('APP_NAME') ?: 'Gazoma Pay',
    'env' => getenv('APP_ENV') ?: 'local',
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'payment_mode' => getenv('PAYMENT_MODE') ?: 'sandbox',
    'currency' => 'GHS',
    'currency_symbol' => 'GH₵',
    'default_fee_percentage' => 1.5, // 1.5%
    'default_fee_fixed' => 0.50, // GH₵ 0.50
];
