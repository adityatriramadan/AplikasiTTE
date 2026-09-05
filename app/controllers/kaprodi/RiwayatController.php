<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Security.php';
require_once BASE_PATH . '/app/models/TandaTanganModel.php';
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';

class RiwayatController {

    public function index(): void {
        Auth::requireRole('kaprodi');

        $ttModel    = new TandaTanganModel();
        $notifModel = new NotifikasiModel();
        $kaprodiId  = (int)$_SESSION['user_id'];
        $search     = Security::sanitize($_GET['search'] ?? '');

        $data = [
            'title'       => 'Riwayat Tanda Tangan — ' . APP_NAME,
            'riwayat'     => $ttModel->getRiwayatKaprodi($kaprodiId, $search),
            'search'      => $search,
            'notif_count' => $notifModel->countBelumDibaca($kaprodiId),
            'current_user'=> Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/kaprodi/riwayat/index.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function detail(int $suratId): void {
        Auth::requireRole('kaprodi');

        $suratModel = new SuratModel();
        $ttModel    = new TandaTanganModel();
        $notifModel = new NotifikasiModel();
        $kaprodiId  = (int)$_SESSION['user_id'];

        $surat      = $suratModel->getById($suratId);
        $tandaTangan = $ttModel->getBySuratId($suratId);

        if (!$surat || !$tandaTangan) {
            header('Location: ' . BASE_URL . '/kaprodi/riwayat');
            exit;
        }

        $data = [
            'title'       => 'Detail Surat — ' . APP_NAME,
            'surat'       => $surat,
            'tanda_tangan'=> $tandaTangan,
            'notif_count' => $notifModel->countBelumDibaca($kaprodiId),
            'current_user'=> Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/kaprodi/riwayat/detail.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }
}
