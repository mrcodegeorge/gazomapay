<?php

require_once __DIR__ . '/../../config/database.php';

class Auth {
    public static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool {
        self::initSession();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function user(): ?array {
        self::initSession();
        if (!self::check()) return null;

        if (!isset($_SESSION['user_data'])) {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT u.*, m.name as merchant_name, m.merchant_id as merchant_code, m.logo as merchant_logo, m.currency, m.environment FROM users u JOIN merchants m ON u.merchant_id = m.id WHERE u.id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $_SESSION['user_data'] = $stmt->fetch();
        }

        return $_SESSION['user_data'] ?: null;
    }

    public static function merchantId(): int {
        $user = self::user();
        return $user ? (int)$user['merchant_id'] : 0;
    }

    public static function login(string $email, string $password): bool {
        self::initSession();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT u.*, m.name as merchant_name, m.merchant_id as merchant_code, m.logo as merchant_logo, m.currency, m.environment FROM users u JOIN merchants m ON u.merchant_id = m.id WHERE u.email = ? AND u.status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['merchant_id'] = $user['merchant_id'];
            $_SESSION['user_data'] = $user;

            // Update last login
            $upd = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $upd->execute([$user['id']]);

            // Audit log
            require_once __DIR__ . '/../Services/AuditLogger.php';
            AuditLogger::log('user.login', "User {$user['email']} logged in successfully");

            return true;
        }

        return false;
    }

    public static function logout(): void {
        self::initSession();
        if (isset($_SESSION['user_id'])) {
            require_once __DIR__ . '/../Services/AuditLogger.php';
            AuditLogger::log('user.logout', "User logged out");
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function generateCsrfToken(): string {
        self::initSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool {
        self::initSession();
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
