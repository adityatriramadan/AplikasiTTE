<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';
require_once BASE_PATH . '/app/models/DistribusiModel.php';

class SekretarisDashboardController {

    public function index(): void {
        Auth::requireRole('sekretaris');

        $suratModel  = new SuratModel();
        $notifModel  = new NotifikasiModel();
        $distModel   = new DistribusiModel();
        $userId      = (int)$_SESSION['user_id'];

        $suratSaya = $suratModel->getAll(['pembuat_id' => $userId]);
        $countStatus = [];
        foreach ($suratSaya as $s) {
            $st = $s['status'];
            $countStatus[$st] = ($countStatus[$st] ?? 0) + 1;
        }

        $data = [
            'title'         => 'Dashboard Sekretaris — ' . APP_NAME,
            'count_status'  => $countStatus,
            'surat_terbaru' => array_slice($suratSaya, 0, 5),
            'notif_list'    => $notifModel->getByUserId($userId, 5),
            'notif_count'   => $notifModel->countBelumDibaca($userId),
            'current_user'  => Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/sekretaris/dashboard.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }
}
