<?php
// End-to-end functional test (models + helpers)
// Run: php tools/e2e_test.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/UserModel.php';
require_once __DIR__ . '/../app/models/TemplateSuratModel.php';
require_once __DIR__ . '/../app/models/SuratModel.php';
require_once __DIR__ . '/../app/models/KunciRSAModel.php';
require_once __DIR__ . '/../app/models/TandaTanganModel.php';
require_once __DIR__ . '/../app/helpers/NomorSuratHelper.php';
require_once __DIR__ . '/../app/helpers/CryptoHelper.php';
require_once __DIR__ . '/../app/helpers/QrHelper.php';
require_once __DIR__ . '/../app/helpers/PdfHelper.php';

try {
    $pdo = getDB();
} catch (Exception $e) {
    fwrite(STDERR, "DB connect failed: " . $e->getMessage() . "\n");
    exit(1);
}

echo "Starting E2E functional test...\n";

$userModel = new UserModel();
$templateModel = new TemplateSuratModel();
$suratModel = new SuratModel();
$kunciModel = new KunciRSAModel();
$ttModel = new TandaTanganModel();

// 1. Pick sekretaris and kaprodi
$sek = $userModel->getByRole('sekretaris');
$kap = $userModel->getByRole('kaprodi');
if (empty($sek) || empty($kap)) {
    echo "Missing required users (sekretaris/kaprodi).\n";
    exit(1);
}
$sekretaris = $sek[0];
$kaprodi = $kap[0];
echo "Sekretaris: " . $sekretaris['nama'] . " (id=" . $sekretaris['id'] . ")\n";
echo "Kaprodi: " . $kaprodi['nama'] . " (id=" . $kaprodi['id'] . ")\n";

// 2. Pick template
$templates = $templateModel->getAktif();
if (empty($templates)) {
    echo "No active templates.\n";
    exit(1);
}
$template = $templates[0];
echo "Using template: " . $template['kode_jenis'] . " - " . $template['nama_jenis'] . "\n";

// 3. Generate nomor surat
$nomor = NomorSuratHelper::generate($template['kode_jenis']);
echo "Generated nomor: $nomor\n";

// 4. Build isi_data from field_dinamis
$fields = json_decode($template['field_dinamis'], true) ?: [];
$isi = [];
foreach ($fields as $f) {
    $name = $f['name'] ?? 'field';
    $type = $f['type'] ?? 'text';
    $isi[$name] = match ($type) {
        'textarea' => 'Contoh isi ' . $name,
        default => 'Contoh ' . $name,
    };
}

// 5. Create surat
$suratData = [
    'nomor_surat' => $nomor,
    'template_id' => $template['id'],
    'perihal'     => 'Surat Uji E2E - ' . time(),
    'isi_data'    => json_encode($isi),
    'pembuat_id'  => $sekretaris['id'],
    'tanggal_surat'=> date('Y-m-d'),
    'status'      => 'menunggu',
];
$suratId = $suratModel->tambah($suratData);
echo "Created surat ID: $suratId\n";

// 6. Ensure kaprodi has RSA key: generate and save
$pin = '1234';
$kp = CryptoHelper::generateKeyPair();
$enc = CryptoHelper::encryptPrivateKey($kp['private_key'], $pin);
$kunciId = $kunciModel->simpan([
    'user_id' => $kaprodi['id'],
    'public_key' => $kp['public_key'],
    'private_key_encrypted' => $enc,
]);
echo "Generated RSA key for kaprodi, kunci_id=$kunciId (PIN=$pin)\n";

// 7. Kaprodi signs the surat (simulate controller)
$verifyUrl = VERIFY_URL . $suratId;
$qrPath = STORAGE_QR . 'qr_' . $suratId . '.svg';
QrHelper::generate($verifyUrl, $qrPath);
echo "QR generated: $qrPath\n";

$pdfPath = STORAGE_PDF . 'surat_' . $suratId . '.pdf';
$pdf = new PdfHelper();
$surat = $suratModel->getById($suratId);
$pdf->renderSuratDenganQr($surat, $qrPath, $pdfPath);
echo "PDF generated: $pdfPath\n";

$hash = CryptoHelper::hashDocument($pdfPath);
echo "PDF hash: $hash\n";

$decrypted = CryptoHelper::decryptPrivateKey($enc, $pin);
$signature = CryptoHelper::signDocument($hash, $decrypted);
echo "Signature (base64 len): " . strlen($signature) . "\n";

$ttId = $ttModel->simpan([
    'surat_id' => $suratId,
    'kaprodi_id' => $kaprodi['id'],
    'hash_sha256' => $hash,
    'signature_rsa' => $signature,
    'public_key_snapshot' => $kp['public_key'],
    'timestamp_tandatangan' => time(),
]);

$ttModel->updatePath($ttId, ['pdf_path' => 'surat_' . $suratId . '.pdf', 'qr_code_url' => $verifyUrl]);
$suratModel->updateStatus($suratId, 'ditandatangani');

echo "Tanda tangan saved (id=$ttId). Verifying...\n";

$tt = $ttModel->getBySuratId($suratId);
$valid = CryptoHelper::verifyDocument($tt['hash_sha256'], $tt['signature_rsa'], $tt['public_key_snapshot']);
echo "Signature valid? " . ($valid ? 'YES' : 'NO') . "\n";

echo "E2E test finished.\n";
