<?php

class Env {
    private static array $variables = [];
    private static bool $loaded = false;

    public static function load(): void {
        if (self::$loaded) {
            return;
        }

        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) {
                    continue;
                }
                if (strpos($line, '=') !== false) {
                    list($key, $val) = explode('=', $line, 2);
                    $key = trim($key);
                    $val = trim(trim($val), '"\'');
                    self::$variables[$key] = $val;
                }
            }
        }

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed {
        self::load();
        if (isset(self::$variables[$key])) {
            $val = self::$variables[$key];
            if (strtolower($val) === 'true') return true;
            if (strtolower($val) === 'false') return false;
            return $val;
        }

        $envVal = getenv($key);
        if ($envVal !== false) {
            if (strtolower($envVal) === 'true') return true;
            if (strtolower($envVal) === 'false') return false;
            return $envVal;
        }

        return $default;
    }
}
