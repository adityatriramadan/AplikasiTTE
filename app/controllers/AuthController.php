<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Security.php';
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/LogAktivitasModel.php';

class AuthController {

    public function login(): void {
        // Jika sudah login, redirect ke dashboard
        if (Auth::isLoggedIn()) {
            Auth::redirectToDashboard();
        }

        $data = [
            'title'   => 'Login — ' . APP_NAME,
            'error'   => $_SESSION['error'] ?? null,
            'expired' => $_GET['expired'] ?? null,
        ];
        unset($_SESSION['error']);

        include BASE_PATH . '/views/layouts/public.php';
        include BASE_PATH . '/views/auth/login.php';
    }

    public function proses(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token tidak valid. Silakan coba lagi.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $nip      = Security::sanitize($_POST['nip'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($nip) || empty($password)) {
            $_SESSION['error'] = 'NIP dan password wajib diisi.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userModel = new UserModel();
        $user      = $userModel->getByNip($nip);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['error'] = 'NIP atau password salah.';
            // Catat percobaan login gagal
            error_log('[Login Gagal] NIP: ' . $nip . ' dari IP: ' . ($_SERVER['REMOTE_ADDR'] ?? ''));
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if ($user['status'] !== 'aktif') {
            $_SESSION['error'] = 'Akun Anda tidak aktif. Hubungi Administrator.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // Set session
        Auth::login($user);

        // Catat log login
        $log = new LogAktivitasModel();
        $log->catat((int)$user['id'], 'login', 'Login berhasil dari IP: ' . ($_SERVER['REMOTE_ADDR'] ?? ''));

        Auth::redirectToDashboard();
    }

    public function logout(): void {
        if (Auth::isLoggedIn()) {
            $log = new LogAktivitasModel();
            $log->catat((int)$_SESSION['user_id'], 'logout', 'Logout dari sistem');
        }
        Auth::logout();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
