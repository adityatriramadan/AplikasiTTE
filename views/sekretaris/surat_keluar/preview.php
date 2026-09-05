<?php
$preview = $data['preview'] ?? [];
$fieldDinamis = $data['field_dinamis'] ?? [];
$flashError = $_SESSION['error'] ?? null;
$flashSuccess = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);
?>
<div class="card">
    <?php if (!empty($flashError)): ?>
    <div style="background:#fff1f0;border:1px solid #fecaca;padding:12px;border-radius:8px;margin-bottom:12px;color:#991b1b;">
        <strong>Terjadi kesalahan:</strong>
        <div style="margin-top:6px;"><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <?php elseif (!empty($flashSuccess)): ?>
    <div style="background:#ecfdf5;border:1px solid #bbf7d0;padding:12px;border-radius:8px;margin-bottom:12px;color:#065f46;">
        <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>
    <h2>Preview Surat</h2>
    <p class="muted">Periksa isi surat sebelum disimpan sebagai draft.</p>
</div>

<div class="card">
    <p><strong>Template:</strong> <?= htmlspecialchars($preview['template']['nama_jenis'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Perihal:</strong> <?= htmlspecialchars($preview['perihal'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Tanggal Surat:</strong> <?= htmlspecialchars($preview['tanggal_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Penerima:</strong> <?= htmlspecialchars($preview['penerima_nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Jabatan:</strong> <?= htmlspecialchars($preview['penerima_jabatan'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Instansi:</strong> <?= htmlspecialchars($preview['penerima_instansi'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>

    <hr>
    <?php $fieldIndex = 0; foreach ($fieldDinamis as $field):
        $fieldName = trim((string)($field['name'] ?? $field['key'] ?? ''));
        $fieldName = $fieldName !== '' ? $fieldName : 'field_' . (++$fieldIndex);
        $fieldLabel = trim((string)($field['label'] ?? $field['title'] ?? ''));
        $fieldLabel = $fieldLabel !== '' ? $fieldLabel : ucwords(str_replace('_', ' ', $fieldName));
    ?>
        <p><strong><?= htmlspecialchars($fieldLabel, ENT_QUOTES, 'UTF-8') ?>:</strong> <?= htmlspecialchars($preview['isi_data'][$fieldName] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    <?php endforeach; ?>
</div>

<div class="card">
    <form method="post" action="<?= BASE_URL ?>/sekretaris/simpan-surat">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="template_id" value="<?= (int)($data['template_id'] ?? 0) ?>">
        <input type="hidden" name="perihal" value="<?= htmlspecialchars($preview['perihal'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="tanggal_surat" value="<?= htmlspecialchars($preview['tanggal_surat'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="penerima_nama" value="<?= htmlspecialchars($preview['penerima_nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="penerima_jabatan" value="<?= htmlspecialchars($preview['penerima_jabatan'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="penerima_instansi" value="<?= htmlspecialchars($preview['penerima_instansi'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <?php $fieldIndex = 0; foreach ($fieldDinamis as $field):
            $fieldName = trim((string)($field['name'] ?? $field['key'] ?? ''));
            $fieldName = $fieldName !== '' ? $fieldName : 'field_' . (++$fieldIndex);
        ?>
            <input type="hidden" name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($preview['isi_data'][$fieldName] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <?php endforeach; ?>
        <a class="btn secondary" href="<?= BASE_URL ?>/sekretaris/isi-form/<?= (int)($data['template_id'] ?? 0) ?>">Kembali</a>
        <button class="btn" type="submit">Simpan Draft</button>
    </form>
</div>