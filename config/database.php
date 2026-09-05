<?php
// Allow overriding via environment variables for local testing
define('DB_HOST', getenv('EOFFICE_DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('EOFFICE_DB_NAME') ?: 'eoffice_unpam');
define('DB_USER', getenv('EOFFICE_DB_USER') ?: 'root');
define('DB_PASS', getenv('EOFFICE_DB_PASS') ?: '');
define('DB_CHARSET', getenv('EOFFICE_DB_CHARSET') ?: 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME .
                   ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            die('Koneksi database gagal. Pastikan MySQL berjalan.');
        }
    }
    return $pdo;
}
