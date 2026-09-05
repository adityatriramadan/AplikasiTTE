<?php
class Auth {

    /**
     * Cek apakah user sudah login
     */
    public static function isLoggedIn(): bool {
        return !empty($_SESSION['user_id']);
    }

    /**
     * Wajibkan login — redirect ke login jika belum login
     */
    public static function requireLogin(): void {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    /**
     * Wajibkan role tertentu — redirect 403 jika role tidak cocok
     * @param string|array $role Role yang diizinkan
     */
    public static function requireRole(string|array $role): void {
        self::requireLogin();
        $roles = is_array($role) ? $role : [$role];
        if (!in_array($_SESSION['user_role'], $roles)) {
            http_response_code(403);
            include BASE_PATH . '/views/auth/error_403.php';
            exit;
        }
    }

    /**
     * Ambil data user yang sedang login dari session
     */
    public static function user(): array {
        return [
            'id'    => $_SESSION['user_id']   ?? null,
            'nama'  => $_SESSION['user_nama']  ?? '',
            'role'  => $_SESSION['user_role']  ?? '',
            'nip'   => $_SESSION['user_nip']   ?? '',
            'email' => $_SESSION['user_email'] ?? '',
        ];
    }

    /**
     * Login: set session user
     */
    public static function login(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_nama']  = $user['nama'];
        $_SESSION['user_role']  = $user['role'];
        $_SESSION['user_nip']   = $user['nip'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['created']    = time();
        $_SESSION['last_activity'] = time();
    }

    /**
     * Logout: destroy session
     */
    public static function logout(): void {
        session_unset();
        session_destroy();
    }

    /**
     * Redirect ke dashboard sesuai role setelah login
     */
    public static function redirectToDashboard(): void {
        $role = $_SESSION['user_role'] ?? '';
        $map = [
            'admin'      => BASE_URL . '/admin/dashboard',
            'kaprodi'    => BASE_URL . '/kaprodi/dashboard',
            'sekretaris' => BASE_URL . '/sekretaris/dashboard',
            'dosen'      => BASE_URL . '/dosen/dashboard',
        ];
        $url = $map[$role] ?? BASE_URL . '/login';
        header('Location: ' . $url);
        exit;
    }
}
