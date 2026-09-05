<?php
// CLI script: import all .sql files in the database/ folder into MySQL
// Usage: php database/import_db.php

if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

require_once __DIR__ . '/../config/database.php';

// Read DB constants from config/database.php
$host = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
$user = defined('DB_USER') ? DB_USER : 'root';
$pass = defined('DB_PASS') ? DB_PASS : '';
$charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';

echo "Connecting to MySQL at $host with user $user\n";

$conn = mysqli_init();
mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
if (!@mysqli_real_connect($conn, $host, $user, $pass, null, ini_get('mysqli.default_port'))) {
    fwrite(STDERR, "Connection failed: " . mysqli_connect_error() . "\n");
    exit(1);
}

mysqli_set_charset($conn, $charset);
// Disable mysqli exceptions to allow graceful handling of SQL errors
mysqli_report(MYSQLI_REPORT_OFF);

// Relax sql_mode for import to avoid errors like 'Invalid default value for ...'
@mysqli_query($conn, "SET SESSION sql_mode = ''");

$dir = __DIR__;
$files = glob($dir . '/*.sql');
if (!$files) {
    echo "No .sql files found in $dir\n";
    exit(0);
}

natsort($files);

foreach ($files as $file) {
    echo "--- Importing: " . basename($file) . "\n";
    $sql = file_get_contents($file);
    if ($sql === false) {
        fwrite(STDERR, "Failed to read $file\n");
        continue;
    }

    // Use mysqli_multi_query to allow multiple statements including CREATE DATABASE
    if (!mysqli_multi_query($conn, $sql)) {
        fwrite(STDERR, "Error importing " . basename($file) . ": " . mysqli_error($conn) . "\n");
        // Continue to next file
        // Clear any pending results
        while (mysqli_more_results($conn) && mysqli_next_result($conn)) { }
        continue;
    }

    // Flush all results
    do {
        if ($res = mysqli_store_result($conn)) {
            mysqli_free_result($res);
        }
    } while (mysqli_more_results($conn) && mysqli_next_result($conn));

    echo "Imported: " . basename($file) . "\n";
}

echo "All done.\n";
mysqli_close($conn);
