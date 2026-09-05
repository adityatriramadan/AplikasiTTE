<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/app/models/LogAktivitasModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';

class LogAktivitasController {

    public function index(): void {
        Auth::requireRole('admin');

        $logModel   = new LogAktivitasModel();
        $notifModel = new NotifikasiModel();

        $filter = [
            'search' => Security::sanitize($_GET['search'] ?? ''),
        ];

        $data = [
            'title'       => 'Log Aktivitas — ' . APP_NAME,
            'logs'        => $logModel->getAll($filter),
            'search'      => $filter['search'],
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user'=> Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/admin/log/index.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }
}
