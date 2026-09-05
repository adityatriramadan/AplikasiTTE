<?php
require_once __DIR__ . '/../config/database.php';
try {
    $pdo = getDB();
} catch (Exception $e) {
    fwrite(STDERR, "DB connect failed: " . $e->getMessage() . "\n");
    exit(1);
}

$count = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
echo "Users count: $count\n";
if ($count > 0) {
    $stmt = $pdo->query('SELECT id, nama, role, email FROM users ORDER BY id');
    foreach ($stmt->fetchAll() as $r) {
        echo $r['id'] . ' | ' . $r['role'] . ' | ' . $r['nama'] . ' | ' . $r['email'] . "\n";
    }
}
