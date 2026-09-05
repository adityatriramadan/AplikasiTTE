<?php
// Allow overriding BASE_URL for local testing (set EOFFICE_BASE_URL env var)
$baseUrlEnv = getenv('EOFFICE_BASE_URL');
if ($baseUrlEnv !== false && !empty($baseUrlEnv)) {
	define('BASE_URL', $baseUrlEnv);
} else {
	define('BASE_URL', 'http://localhost/eoffice');
}
define('BASE_PATH', dirname(__DIR__));
define('STORAGE_KEYS', BASE_PATH . '/storage/keys/');
define('STORAGE_PDF',  BASE_PATH . '/public/pdf/');
define('STORAGE_QR',   BASE_PATH . '/public/qr/');
define('SESSION_LIFETIME', 3600); // 1 jam dalam detik
define('RSA_BITS', 2048);
define('VERIFY_URL', BASE_URL . '/verifikasi?id=');
define('APP_NAME', 'E-Office TI UNPAM');
define('APP_VERSION', '1.0');
