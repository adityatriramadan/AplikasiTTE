<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/UserModel.php';

try {
    $pdo = getDB();
} catch (Exception $e) {
    fwrite(STDERR, "DB connect failed: " . $e->getMessage() . "\n");
    exit(1);
}

$count = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
if ($count > 0) {
    echo "Users already exist ($count). Skipping seeding.\n";
    exit(0);
}

$userModel = new UserModel();
$demo = [
    ['nama'=>'Administrator Sistem','nip'=>'admin001','jabatan'=>'Administrator','role'=>'admin','email'=>'admin@ti.unpam.ac.id','password'=>'Admin123!'],
    ['nama'=>'Dr. Hendra Kusuma, M.Kom','nip'=>'kaprodi001','jabatan'=>'Ketua Program Studi','role'=>'kaprodi','email'=>'kaprodi@ti.unpam.ac.id','password'=>'Admin123!'],
    ['nama'=>'Siti Rahayu, S.Kom','nip'=>'sekretaris001','jabatan'=>'Sekretaris TU','role'=>'sekretaris','email'=>'sekretaris@ti.unpam.ac.id','password'=>'Admin123!'],
    ['nama'=>'Budi Santoso, S.Kom., M.T','nip'=>'dosen001','jabatan'=>'Dosen Tetap','role'=>'dosen','email'=>'budi@ti.unpam.ac.id','password'=>'Admin123!'],
];

foreach ($demo as $d) {
    $id = $userModel->tambah($d);
    echo "Inserted user id=$id email={$d['email']} role={$d['role']}\n";
}

echo "Seeding complete.\n";
