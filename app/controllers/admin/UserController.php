<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Security.php';
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/LogAktivitasModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';

class UserController {

    private UserModel $userModel;
    private LogAktivitasModel $logModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->logModel  = new LogAktivitasModel();
    }

    public function index(): void {
        Auth::requireRole('admin');
        $notifModel = new NotifikasiModel();
        $data = [
            'title'       => 'Manajemen User — ' . APP_NAME,
            'users'       => $this->userModel->getAll(),
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user'=> Auth::user(),
            'success'     => $_SESSION['success'] ?? null,
            'error'       => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['success'], $_SESSION['error']);

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/admin/user/index.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function tambah(): void {
        Auth::requireRole('admin');
        $notifModel = new NotifikasiModel();
        $data = [
            'title'       => 'Tambah User — ' . APP_NAME,
            'csrf_token'  => Security::generateCsrfToken(),
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user'=> Auth::user(),
            'error'       => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['error']);

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/admin/user/tambah.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function simpan(): void {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/users/tambah');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $nama     = Security::sanitize($_POST['nama'] ?? '');
        $nip      = Security::sanitize($_POST['nip'] ?? '');
        $jabatan  = Security::sanitize($_POST['jabatan'] ?? '');
        $role     = Security::sanitize($_POST['role'] ?? '');
        $email    = Security::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $validRoles = ['admin', 'kaprodi', 'sekretaris', 'dosen'];
        if (!$nama || !$nip || !$jabatan || !in_array($role, $validRoles) || !$email || !$password) {
            $_SESSION['error'] = 'Semua field wajib diisi dengan benar.';
            header('Location: ' . BASE_URL . '/admin/users/tambah');
            exit;
        }

        if ($this->userModel->nipExists($nip)) {
            $_SESSION['error'] = 'NIP sudah digunakan.';
            header('Location: ' . BASE_URL . '/admin/users/tambah');
            exit;
        }

        if ($this->userModel->emailExists($email)) {
            $_SESSION['error'] = 'Email sudah digunakan.';
            header('Location: ' . BASE_URL . '/admin/users/tambah');
            exit;
        }

        $id = $this->userModel->tambah([
            'nama'     => $nama,
            'nip'      => $nip,
            'jabatan'  => $jabatan,
            'role'     => $role,
            'email'    => $email,
            'password' => $password,
            'status'   => 'aktif',
        ]);

        $this->logModel->catat((int)$_SESSION['user_id'], 'tambah_user', 'Menambah user: ' . $nama . ' (' . $role . ')');
        $_SESSION['success'] = 'User ' . $nama . ' berhasil ditambahkan.';
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function edit(int $id): void {
        Auth::requireRole('admin');
        $user = $this->userModel->getById($id);
        if (!$user) {
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }
        $notifModel = new NotifikasiModel();
        $data = [
            'title'       => 'Edit User — ' . APP_NAME,
            'user'        => $user,
            'csrf_token'  => Security::generateCsrfToken(),
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user'=> Auth::user(),
            'error'       => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['error']);

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/admin/user/edit.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function update(int $id): void {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/users/edit/' . $id);
            exit;
        }
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $nama    = Security::sanitize($_POST['nama'] ?? '');
        $jabatan = Security::sanitize($_POST['jabatan'] ?? '');
        $role    = Security::sanitize($_POST['role'] ?? '');
        $email   = Security::sanitize($_POST['email'] ?? '');
        $status  = Security::sanitize($_POST['status'] ?? 'aktif');

        if ($this->userModel->emailExists($email, $id)) {
            $_SESSION['error'] = 'Email sudah digunakan oleh user lain.';
            header('Location: ' . BASE_URL . '/admin/users/edit/' . $id);
            exit;
        }

        $this->userModel->update($id, [
            'nama'    => $nama,
            'jabatan' => $jabatan,
            'role'    => $role,
            'email'   => $email,
            'status'  => $status,
        ]);

        if (!empty($_POST['password'])) {
            $this->userModel->updatePassword($id, $_POST['password']);
        }

        $this->logModel->catat((int)$_SESSION['user_id'], 'edit_user', 'Mengedit user ID: ' . $id);
        $_SESSION['success'] = 'Data user berhasil diperbarui.';
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function toggle(int $id): void {
        Auth::requireRole('admin');
        if ((int)$_SESSION['user_id'] === $id) {
            $_SESSION['error'] = 'Tidak dapat menonaktifkan akun sendiri.';
        } else {
            $this->userModel->toggleStatus($id);
            $this->logModel->catat((int)$_SESSION['user_id'], 'toggle_user', 'Toggle status user ID: ' . $id);
            $_SESSION['success'] = 'Status user berhasil diubah.';
        }
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }
}
