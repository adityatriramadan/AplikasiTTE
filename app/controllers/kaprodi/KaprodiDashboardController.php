<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/TandaTanganModel.php';
require_once BASE_PATH . '/app/models/KunciRSAModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';

class KaprodiDashboardController {

    public function index(): void {
        Auth::requireRole('kaprodi');

        $suratModel = new SuratModel();
        $ttModel    = new TandaTanganModel();
        $kunciModel = new KunciRSAModel();
        $notifModel = new NotifikasiModel();
        $kaprodiId  = (int)$_SESSION['user_id'];

        $data = [
            'title'           => 'Dashboard Kaprodi — ' . APP_NAME,
            'jumlah_surat'    => $suratModel->countByStatus(),
            'surat_menunggu'  => $suratModel->getSuratMenunggu(),
            'tanda_bulan_ini' => $ttModel->countThisMonth($kaprodiId),
            'has_kunci'       => $kunciModel->hasAktifKey($kaprodiId),
            'notif_list'      => $notifModel->getByUserId($kaprodiId, 5),
            'notif_count'     => $notifModel->countBelumDibaca($kaprodiId),
            'current_user'    => Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/kaprodi/dashboard.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }
}
