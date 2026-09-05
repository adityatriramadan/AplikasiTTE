<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Security.php';
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/TandaTanganModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';

class DokumenController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function index(): void {
        Auth::requireRole('dosen');

        $userId = (int)$_SESSION['user_id'];
        $suratModel = new SuratModel();
        $notifModel = new NotifikasiModel();

        $data = [
            'title' => 'Dokumen Saya - ' . APP_NAME,
            'dokumen' => $suratModel->getForDosen($userId),
            'notif_count' => $notifModel->countBelumDibaca($userId),
            'current_user' => Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/dosen/dokumen/index.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function detail(int $suratId): void {
        Auth::requireRole('dosen');

        $userId = (int)$_SESSION['user_id'];
        $stmt = $this->pdo->prepare(
            'SELECT d.id AS distribusi_id, s.*, t.nama_jenis, u.nama AS nama_pembuat, d.status_baca, d.tanggal_kirim
             FROM distribusi d
             JOIN surat s ON d.surat_id = s.id
             JOIN template_surat t ON s.template_id = t.id
             JOIN users u ON s.pembuat_id = u.id
             WHERE d.surat_id = ? AND d.penerima_id = ?
             ORDER BY d.tanggal_kirim DESC
             LIMIT 1'
        );
        $stmt->execute([$suratId, $userId]);
        $dokumen = $stmt->fetch();

        if (!$dokumen) {
            header('Location: ' . BASE_URL . '/dosen/dokumen');
            exit;
        }

        $ttModel = new TandaTanganModel();
        $tandaTangan = $ttModel->getBySuratId($suratId);

        if (!empty($dokumen['status_baca']) && $dokumen['status_baca'] === 'belum') {
            $update = $this->pdo->prepare('UPDATE distribusi SET status_baca = "dibaca", dibaca_pada = NOW() WHERE id = ?');
            $update->execute([(int)$dokumen['distribusi_id']]);
        }

        $notifModel = new NotifikasiModel();
        $data = [
            'title' => 'Detail Dokumen - ' . APP_NAME,
            'dokumen' => $dokumen,
            'tanda_tangan' => $tandaTangan,
            'notif_count' => $notifModel->countBelumDibaca($userId),
            'current_user' => Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/dosen/dokumen/detail.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }
}