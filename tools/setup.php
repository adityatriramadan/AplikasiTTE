<?php
// Simple setup checker for eOffice project
// Run: php tools/setup.php

echo "eOffice setup check\n";

$required = [
    'pdo_mysql' => 'PDO MySQL extension',
    'openssl'   => 'OpenSSL (for RSA/crypto)',
    'mbstring'  => 'mbstring',
    'gd'        => 'GD (image/QR generation)'
];

foreach ($required as $ext => $desc) {
    echo "- Checking $desc... ";
    if (!extension_loaded($ext)) {
        echo "MISSING\n";
    } else {
        echo "OK\n";
    }
}

$dirs = [
    'public/pdf',
    'public/qr',
    'storage/keys'
];

foreach ($dirs as $d) {
    $path = __DIR__ . '/../' . $d;
    echo "- Ensuring directory $d... ";
    if (!is_dir($path)) {
        if (@mkdir($path, 0775, true)) {
            echo "created\n";
        } else {
            echo "FAILED to create ($path)\n";
            continue;
        }
    } else {
        echo "exists\n";
    }
    // try to set writable
    if (!is_writable($path)) {
        @chmod($path, 0775);
        echo "  (set permissions)\n";
    }
}

echo "Setup check complete. Next: import database (see database/README.md) and run app via XAMPP.\n";
