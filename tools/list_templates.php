<?php
require_once __DIR__ . '/../config/database.php';
try {
    $pdo = getDB();
} catch (Exception $e) {
    fwrite(STDERR, "DB connect failed: " . $e->getMessage() . "\n");
    exit(1);
}

$stmt = $pdo->query('SELECT id,kode_jenis,nama_jenis,status FROM template_surat ORDER BY id');
foreach ($stmt->fetchAll() as $r) {
    echo $r['id'] . ' | ' . $r['kode_jenis'] . ' | ' . $r['nama_jenis'] . ' | ' . $r['status'] . "\n";
}
