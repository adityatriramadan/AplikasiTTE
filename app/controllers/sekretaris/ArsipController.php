<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Security.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';

class ArsipController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function keluar(): void {
        Auth::requireRole('sekretaris');

        $search = Security::sanitize($_GET['search'] ?? '');
        $sql = 'SELECT s.*, t.nama_jenis, u.nama AS nama_pembuat
                FROM surat s
                JOIN template_surat t ON s.template_id = t.id
                JOIN users u ON s.pembuat_id = u.id
                WHERE s.status IN ("ditandatangani", "didistribusikan")';
        $params = [];
        if ($search !== '') {
            $sql .= ' AND (s.nomor_surat LIKE ? OR s.perihal LIKE ? OR u.nama LIKE ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY s.created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $notifModel = new NotifikasiModel();
        $data = [
            'title' => 'Arsip Surat Keluar - ' . APP_NAME,
            'arsip' => $stmt->fetchAll(),
            'search' => $search,
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user' => Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/sekretaris/arsip/keluar.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function masuk(): void {
        Auth::requireRole('sekretaris');

        $search = Security::sanitize($_GET['search'] ?? '');
        $sql = 'SELECT sm.*, u.nama AS nama_input
                FROM surat_masuk sm
                JOIN users u ON sm.input_oleh = u.id';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE (sm.nomor_surat LIKE ? OR sm.pengirim LIKE ? OR sm.perihal LIKE ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY sm.tanggal_terima DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $notifModel = new NotifikasiModel();
        $data = [
            'title' => 'Arsip Surat Masuk - ' . APP_NAME,
            'arsip' => $stmt->fetchAll(),
            'search' => $search,
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user' => Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/sekretaris/arsip/masuk.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }
}