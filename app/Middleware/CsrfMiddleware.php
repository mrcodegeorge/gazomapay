<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Response.php';

class CsrfMiddleware {
    public static function handle(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!Auth::verifyCsrfToken($token)) {
                Response::json(['error' => 'CSRF verification failed'], 403);
            }
        }
    }
}
