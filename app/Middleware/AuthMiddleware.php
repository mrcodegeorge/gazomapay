<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Response.php';

class AuthMiddleware {
    public static function handle(): void {
        if (!Auth::check()) {
            Response::redirect('/login');
        }
    }
}
