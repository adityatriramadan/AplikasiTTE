<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Security.php';
require_once BASE_PATH . '/app/models/TemplateSuratModel.php';
require_once BASE_PATH . '/app/models/LogAktivitasModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';

class TemplateSuratController {

    private TemplateSuratModel $templateModel;
    private LogAktivitasModel $logModel;

    public function __construct() {
        $this->templateModel = new TemplateSuratModel();
        $this->logModel      = new LogAktivitasModel();
    }

    public function index(): void {
        Auth::requireRole('admin');
        $notifModel = new NotifikasiModel();
        $data = [
            'title'       => 'Template Surat — ' . APP_NAME,
            'templates'   => $this->templateModel->getAll(),
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user'=> Auth::user(),
            'success'     => $_SESSION['success'] ?? null,
            'error'       => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['success'], $_SESSION['error']);

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/admin/template/index.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function tambah(): void {
        Auth::requireRole('admin');
        $notifModel = new NotifikasiModel();
        $data = [
            'title'       => 'Tambah Template — ' . APP_NAME,
            'csrf_token'  => Security::generateCsrfToken(),
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user'=> Auth::user(),
            'error'       => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['error']);

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/admin/template/tambah.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function simpan(): void {
        Auth::requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/template/tambah');
            exit;
        }
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $kode         = strtoupper(Security::sanitize($_POST['kode_jenis'] ?? ''));
        $nama         = Security::sanitize($_POST['nama_jenis'] ?? '');
        $konten       = $_POST['konten_template'] ?? '';
        $formatNomor  = Security::sanitize($_POST['format_nomor'] ?? '');
        $fieldDinamis = $_POST['field_dinamis'] ?? '[]';

        if (!$kode || !$nama || !$konten || !$formatNomor) {
            $_SESSION['error'] = 'Semua field wajib diisi.';
            header('Location: ' . BASE_URL . '/admin/template/tambah');
            exit;
        }

        if ($this->templateModel->kodeExists($kode)) {
            $_SESSION['error'] = 'Kode jenis surat sudah ada.';
            header('Location: ' . BASE_URL . '/admin/template/tambah');
            exit;
        }

        // Validasi JSON field_dinamis
        if (!json_decode($fieldDinamis)) {
            $fieldDinamis = '[]';
        }

        $this->templateModel->tambah([
            'kode_jenis'      => $kode,
            'nama_jenis'      => $nama,
            'konten_template' => $konten,
            'format_nomor'    => $formatNomor,
            'field_dinamis'   => $fieldDinamis,
            'status'          => 'aktif',
        ]);

        $this->logModel->catat((int)$_SESSION['user_id'], 'tambah_template', 'Menambah template: ' . $nama);
        $_SESSION['success'] = 'Template surat berhasil ditambahkan.';
        header('Location: ' . BASE_URL . '/admin/template');
        exit;
    }

    public function edit(int $id): void {
        Auth::requireRole('admin');
        $template = $this->templateModel->getById($id);
        if (!$template) {
            header('Location: ' . BASE_URL . '/admin/template');
            exit;
        }
        $notifModel = new NotifikasiModel();
        $data = [
            'title'       => 'Edit Template — ' . APP_NAME,
            'template'    => $template,
            'csrf_token'  => Security::generateCsrfToken(),
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user'=> Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/admin/template/edit.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function update(int $id): void {
        Auth::requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/template/edit/' . $id);
            exit;
        }
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $nama        = Security::sanitize($_POST['nama_jenis'] ?? '');
        $konten      = $_POST['konten_template'] ?? '';
        $formatNomor = Security::sanitize($_POST['format_nomor'] ?? '');
        $fieldDinamis = $_POST['field_dinamis'] ?? '[]';
        $status      = Security::sanitize($_POST['status'] ?? 'aktif');

        if (!json_decode($fieldDinamis)) {
            $fieldDinamis = '[]';
        }

        $this->templateModel->update($id, [
            'nama_jenis'      => $nama,
            'konten_template' => $konten,
            'format_nomor'    => $formatNomor,
            'field_dinamis'   => $fieldDinamis,
            'status'          => $status,
        ]);

        $this->logModel->catat((int)$_SESSION['user_id'], 'edit_template', 'Edit template ID: ' . $id);
        $_SESSION['success'] = 'Template berhasil diperbarui.';
        header('Location: ' . BASE_URL . '/admin/template');
        exit;
    }

    public function toggle(int $id): void {
        Auth::requireRole('admin');
        $this->templateModel->toggleStatus($id);
        $this->logModel->catat((int)$_SESSION['user_id'], 'toggle_template', 'Toggle status template ID: ' . $id);
        $_SESSION['success'] = 'Status template diubah.';
        header('Location: ' . BASE_URL . '/admin/template');
        exit;
    }
}
