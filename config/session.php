<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);   // Set 1 jika pakai HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_start();

// Cek session expired
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > SESSION_LIFETIME) {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/login?expired=1');
        exit;
    }
}
$_SESSION['last_activity'] = time();

// Regenerate session ID setiap 30 menit (cegah session fixation)
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
