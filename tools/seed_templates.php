<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/TemplateSuratModel.php';

try {
    $pdo = getDB();
} catch (Exception $e) {
    fwrite(STDERR, "DB connect failed: " . $e->getMessage() . "\n");
    exit(1);
}

$count = (int)$pdo->query('SELECT COUNT(*) FROM template_surat')->fetchColumn();
if ($count > 0) {
    echo "Templates already exist ($count). Skipping seeding.\n";
    exit(0);
}

$model = new TemplateSuratModel();
$fieldJson = json_encode([
    ['name'=>'nama_pegawai','label'=>'Nama Pegawai','type'=>'text','required'=>true],
    ['name'=>'nip_pegawai','label'=>'NIP Pegawai','type'=>'text','required'=>true],
]);

$id = $model->tambah([
    'kode_jenis' => 'ST',
    'nama_jenis' => 'Surat Tugas (Demo)',
    'konten_template' => '<div><h3>SURAT TUGAS</h3><p>Nomor: {{nomor_surat}}</p><p>Nama: {{nama_pegawai}}</p></div>',
    'format_nomor' => '{urut}/ST-TI.UNPAM/{bulan_romawi}/{tahun}',
    'field_dinamis' => $fieldJson,
]);

echo "Inserted demo template id=$id\n";
