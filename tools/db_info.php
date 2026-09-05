<?php
require_once __DIR__ . '/../config/database.php';
try {
    $pdo = getDB();
} catch (Exception $e) {
    fwrite(STDERR, "DB connect failed: " . $e->getMessage() . "\n");
    exit(1);
}

$row = $pdo->query('SELECT DATABASE() AS db')->fetch();
echo "Connected to DB: " . ($row['db'] ?? '(unknown)') . "\n";

$tables = $pdo->query("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = DATABASE() ORDER BY TABLE_NAME")->fetchAll(PDO::FETCH_COLUMN);
echo "Tables in DB:\n";
foreach ($tables as $t) echo " - $t\n";
