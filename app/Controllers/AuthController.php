<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/View.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/RateLimiter.php';
require_once __DIR__ . '/../Middleware/CsrfMiddleware.php';
require_once __DIR__ . '/../../config/database.php';

class AuthController {
    public function showLogin(): void {
        if (Auth::check()) {
            Response::redirect('/dashboard');
        }
        View::render('auth/login', ['pageTitle' => 'Login'], 'auth');
    }

    public function processLogin(): void {
        CsrfMiddleware::handle();
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Rate limiting: max 5 login attempts per minute per IP
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if (!RateLimiter::check('login_' . $ip, 5, 60)) {
            Response::setFlash('error', 'Too many failed login attempts. Please wait 60 seconds.');
            Response::redirect('/login');
        }

        if (Auth::login($email, $password)) {
            Response::redirect('/dashboard');
        } else {
            Response::setFlash('error', 'Invalid email or password credentials');
            Response::redirect('/login');
        }
    }

    public function showRegister(): void {
        if (Auth::check()) {
            Response::redirect('/dashboard');
        }
        View::render('auth/register', ['pageTitle' => 'Register Business'], 'auth');
    }

    public function processRegister(): void {
        CsrfMiddleware::handle();
        $companyName = trim($_POST['company_name'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($companyName) || empty($email) || empty($password)) {
            Response::setFlash('error', 'Please fill all required fields');
            Response::redirect('/register');
        }

        $pdo = Database::getConnection();

        // Check if user exists
        $stmtChk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmtChk->execute([$email]);
        if ($stmtChk->fetch()) {
            Response::setFlash('error', 'Email is already registered.');
            Response::redirect('/register');
        }

        // Create Merchant
        $mchUuid = 'mch_' . bin2hex(random_bytes(6));
        $mchCode = 'GZM_' . rand(100000, 999999);
        $stmtMch = $pdo->prepare("INSERT INTO merchants (uuid, merchant_id, name, email) VALUES (?, ?, ?, ?)");
        $stmtMch->execute([$mchUuid, $mchCode, $companyName, $email]);
        $merchantId = $pdo->lastInsertId();

        // Create Owner User
        $userUuid = 'usr_' . bin2hex(random_bytes(6));
        $passHash = password_hash($password, PASSWORD_BCRYPT);
        $stmtUsr = $pdo->prepare("INSERT INTO users (merchant_id, uuid, name, email, password, role) VALUES (?, ?, ?, ?, ?, 'owner')");
        $stmtUsr->execute([$merchantId, $userUuid, $name, $email, $passHash]);

        Auth::login($email, $password);
        Response::setFlash('success', 'Welcome to Gazoma Pay! Please complete your merchant onboarding.');
        Response::redirect('/onboarding');
    }

    public function logout(): void {
        Auth::logout();
        Response::redirect('/login');
    }
}
