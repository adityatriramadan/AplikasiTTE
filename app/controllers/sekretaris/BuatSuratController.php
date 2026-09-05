<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Security.php';
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/TemplateSuratModel.php';
require_once BASE_PATH . '/app/models/LogAktivitasModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';
require_once BASE_PATH . '/app/helpers/NomorSuratHelper.php';
require_once BASE_PATH . '/app/helpers/NotifikasiHelper.php';
require_once BASE_PATH . '/app/models/UserModel.php';

class BuatSuratController {

    private function normalizeField(array $field, int $index): array {
        $rawName  = trim((string)($field['name'] ?? $field['key'] ?? ''));
        $rawLabel = trim((string)($field['label'] ?? $field['title'] ?? ''));
        $name     = $rawName !== '' ? $rawName : 'field_' . ($index + 1);
        $label    = $rawLabel !== '' ? $rawLabel : ucwords(str_replace('_', ' ', $name));

        return [
            'name' => $name,
            'label' => $label,
            'type' => $field['type'] ?? 'text',
            'required' => !empty($field['required']),
        ];
    }

    /**
     * Pilih template surat
     */
    public function index(): void {
        Auth::requireRole('sekretaris');
        $templateModel = new TemplateSuratModel();
        $notifModel    = new NotifikasiModel();

        $data = [
            'title'       => 'Buat Surat Baru — ' . APP_NAME,
            'templates'   => $templateModel->getAktif(),
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user'=> Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/sekretaris/surat_keluar/buat.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    /**
     * Form isi data surat sesuai template
     */
    public function isiForm(int $templateId): void {
        Auth::requireRole('sekretaris');
        $templateModel = new TemplateSuratModel();
        $template      = $templateModel->getById($templateId);
        $notifModel    = new NotifikasiModel();

        if (!$template || $template['status'] !== 'aktif') {
            header('Location: ' . BASE_URL . '/sekretaris/buat-surat');
            exit;
        }

        $fieldDinamis = json_decode($template['field_dinamis'], true) ?? [];

        $data = [
            'title'        => 'Isi Form Surat — ' . APP_NAME,
            'template'     => $template,
            'field_dinamis'=> $fieldDinamis,
            'csrf_token'   => Security::generateCsrfToken(),
            'notif_count'  => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user' => Auth::user(),
            'tanggal_hari_ini' => date('Y-m-d'),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/sekretaris/surat_keluar/isi_form.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    /**
     * Preview surat sebelum disimpan sebagai draft
     */
    public function preview(): void {
        Auth::requireRole('sekretaris');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/sekretaris/buat-surat');
            exit;
        }
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $templateId = (int)($_POST['template_id'] ?? 0);
        $templateModel = new TemplateSuratModel();
        $template = $templateModel->getById($templateId);
        if (!$template || $template['status'] !== 'aktif') {
            $_SESSION['error'] = 'Template tidak valid.';
            header('Location: ' . BASE_URL . '/sekretaris/buat-surat');
            exit;
        }

        $fieldDinamis = json_decode($template['field_dinamis'], true) ?? [];
        $isiData = [];
        $fieldIndex = 0;
        foreach ($fieldDinamis as $field) {
            $normalizedField = $this->normalizeField((array)$field, $fieldIndex++);
            $key = $normalizedField['name'];
            $isiData[$key] = Security::sanitize($_POST[$key] ?? '');
        }

        $previewData = [
            'template' => $template,
            'perihal' => Security::sanitize($_POST['perihal'] ?? ''),
            'tanggal_surat' => Security::sanitize($_POST['tanggal_surat'] ?? date('Y-m-d')),
            'penerima_nama' => Security::sanitize($_POST['penerima_nama'] ?? ''),
            'penerima_jabatan' => Security::sanitize($_POST['penerima_jabatan'] ?? ''),
            'penerima_instansi' => Security::sanitize($_POST['penerima_instansi'] ?? ''),
            'isi_data' => $isiData,
        ];

        $notifModel = new NotifikasiModel();
        $data = [
            'title' => 'Preview Surat — ' . APP_NAME,
            'preview' => $previewData,
            'template_id' => $templateId,
            'field_dinamis' => $fieldDinamis,
            'csrf_token' => Security::generateCsrfToken(),
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user' => Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/sekretaris/surat_keluar/preview.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    /**
     * Simpan surat sebagai draft
     */
    public function simpan(): void {
        Auth::requireRole('sekretaris');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/sekretaris/buat-surat');
            exit;
        }
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $templateId = (int)($_POST['template_id'] ?? 0);
        $perihal    = Security::sanitize($_POST['perihal'] ?? '');
        $tanggal    = Security::sanitize($_POST['tanggal_surat'] ?? date('Y-m-d'));

        $templateModel = new TemplateSuratModel();
        $template      = $templateModel->getById($templateId);
        if (!$template) {
            die('Template tidak valid.');
        }

        $fieldDinamis = json_decode($template['field_dinamis'], true) ?? [];
        $isiData = [];
        $fieldIndex = 0;
        foreach ($fieldDinamis as $field) {
            $normalizedField = $this->normalizeField((array)$field, $fieldIndex++);
            $key = $normalizedField['name'];
            $isiData[$key] = Security::sanitize($_POST[$key] ?? '');
        }

        // Generate nomor surat otomatis
        $nomorSurat = NomorSuratHelper::generate($template['kode_jenis']);

        $suratModel = new SuratModel();
        $suratId    = $suratModel->tambah([
            'nomor_surat'       => $nomorSurat,
            'template_id'       => $templateId,
            'perihal'           => $perihal,
            'isi_data'          => json_encode($isiData),
            'pembuat_id'        => (int)$_SESSION['user_id'],
            'penerima_nama'     => Security::sanitize($_POST['penerima_nama'] ?? ''),
            'penerima_jabatan'  => Security::sanitize($_POST['penerima_jabatan'] ?? ''),
            'penerima_instansi' => Security::sanitize($_POST['penerima_instansi'] ?? ''),
            'status'            => 'draft',
            'tanggal_surat'     => $tanggal,
        ]);

        $logModel = new LogAktivitasModel();
        $logModel->catat((int)$_SESSION['user_id'], 'buat_surat', 'Membuat surat: ' . $nomorSurat . ' — ' . $perihal);

        $_SESSION['success'] = 'Surat berhasil disimpan sebagai draft. Nomor: ' . $nomorSurat;
        header('Location: ' . BASE_URL . '/sekretaris/surat-keluar/detail/' . $suratId);
        exit;
    }

    /**
     * Ajukan surat ke Kaprodi (ubah status draft → menunggu)
     */
    public function ajukan(): void {
        Auth::requireRole('sekretaris');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/sekretaris/surat-keluar/daftar');
            exit;
        }
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $suratId    = (int)($_POST['surat_id'] ?? 0);
        $suratModel = new SuratModel();
        $surat      = $suratModel->getById($suratId);

        if (!$surat || (int)$surat['pembuat_id'] !== (int)$_SESSION['user_id']) {
            $_SESSION['error'] = 'Surat tidak ditemukan atau bukan milik Anda.';
            header('Location: ' . BASE_URL . '/sekretaris/surat-keluar/daftar');
            exit;
        }

        if ($surat['status'] !== 'draft' && $surat['status'] !== 'ditolak') {
            $_SESSION['error'] = 'Surat ini tidak dapat diajukan ulang.';
            header('Location: ' . BASE_URL . '/sekretaris/surat-keluar/detail/' . $suratId);
            exit;
        }

        $suratModel->updateStatus($suratId, 'menunggu');

        // Kirim notifikasi ke semua kaprodi
        $userModel   = new UserModel();
        $kaprodiList = $userModel->getByRole('kaprodi');
        foreach ($kaprodiList as $kaprodi) {
            NotifikasiHelper::kirim(
                (int)$kaprodi['id'],
                'Surat baru menunggu tanda tangan: "' . $surat['perihal'] . '"',
                BASE_URL . '/kaprodi/review/' . $suratId
            );
        }

        $logModel = new LogAktivitasModel();
        $logModel->catat((int)$_SESSION['user_id'], 'ajukan_surat', 'Mengajukan surat ID: ' . $suratId . ' ke Kaprodi');

        $_SESSION['success'] = 'Surat berhasil diajukan ke Kaprodi untuk ditandatangani.';
        header('Location: ' . BASE_URL . '/sekretaris/surat-keluar/detail/' . $suratId);
        exit;
    }
}
