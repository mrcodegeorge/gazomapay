<?php

class RateLimiter {
    public static function check(string $key, int $maxAttempts = 5, int $decaySeconds = 60): bool {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $now = time();
        $attempts = $_SESSION['rate_limit'][$key] ?? [];

        // Filter out expired attempts
        $attempts = array_filter($attempts, function($timestamp) use ($now, $decaySeconds) {
            return ($now - $timestamp) < $decaySeconds;
        });

        if (count($attempts) >= $maxAttempts) {
            return false;
        }

        $attempts[] = $now;
        $_SESSION['rate_limit'][$key] = $attempts;

        return true;
    }
}
