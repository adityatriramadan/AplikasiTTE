<?php
// Additional E2E tests: distribusi & notifikasi
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/DistribusiModel.php';
require_once __DIR__ . '/../app/models/NotifikasiModel.php';
require_once __DIR__ . '/../app/models/SuratModel.php';

try { $pdo = getDB(); } catch (Exception $e) { fwrite(STDERR, $e->getMessage()."\n"); exit(1);} 

$suratModel = new SuratModel();
$disModel = new DistribusiModel();
$notifModel = new NotifikasiModel();

$surat = $suratModel->getById(1);
if (!$surat) { echo "Surat id=1 not found\n"; exit(1); }

// find a dosen user
$stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'dosen' LIMIT 1"); $stmt->execute(); $dosenId = $stmt->fetchColumn();
if (!$dosenId) { echo "No dosen user found\n"; exit(1); }

// kirim internal
$distId1 = $disModel->kirim((int)$surat['id'], (int)$dosenId);
echo "Distribusi internal created id=$distId1 to user=$dosenId\n";

// kirim eksternal
$distId2 = $disModel->kirim((int)$surat['id'], null, 'PT. Eksternal Contoh');
echo "Distribusi eksternal created id=$distId2\n";

// create notifikasi
$notifId = $notifModel->kirim((int)$dosenId, 'Anda menerima surat: ' . $surat['perihal'], '/');
echo "Notifikasi created id=$notifId for user $dosenId\n";

// count unread distribusi for dosen
$cnt = $disModel->countBelumDibaca((int)$dosenId);
echo "Distribusi belum dibaca untuk user $dosenId: $cnt\n";

// list latest notifikasi
$notifs = $notifModel->getByUserId((int)$dosenId, 10);
foreach ($notifs as $n) echo "NOTIF: {$n['id']} | {$n['pesan']} | is_dibaca={$n['is_dibaca']}\n";

echo "e2e_more finished.\n";
