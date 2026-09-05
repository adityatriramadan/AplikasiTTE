<?php
class UserModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function getAll(): array {
        $stmt = $this->pdo->prepare("SELECT id, nama, nip, jabatan, role, email, status, created_at FROM users ORDER BY role, nama");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByNip(string $nip): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE nip = ? AND status = 'aktif'");
        $stmt->execute([$nip]);
        return $stmt->fetch();
    }

    public function getByEmail(string $email): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getByRole(string $role): array {
        $stmt = $this->pdo->prepare("SELECT id, nama, nip, jabatan, email, status FROM users WHERE role = ? ORDER BY nama");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    public function tambah(array $data): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (nama, nip, jabatan, role, email, password, status) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['nama'],
            $data['nip'],
            $data['jabatan'],
            $data['role'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            $data['status'] ?? 'aktif',
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET nama=?, jabatan=?, role=?, email=?, status=? WHERE id=?"
        );
        return $stmt->execute([
            $data['nama'],
            $data['jabatan'],
            $data['role'],
            $data['email'],
            $data['status'],
            $id,
        ]);
    }

    public function updatePassword(int $id, string $password): bool {
        $stmt = $this->pdo->prepare("UPDATE users SET password=? WHERE id=?");
        return $stmt->execute([password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]), $id]);
    }

    public function toggleStatus(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE users SET status = IF(status='aktif','nonaktif','aktif') WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function countByRole(): array {
        $stmt = $this->pdo->prepare("SELECT role, COUNT(*) as total FROM users WHERE status='aktif' GROUP BY role");
        $stmt->execute();
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['role']] = (int)$row['total'];
        }
        return $result;
    }

    public function nipExists(string $nip, int $excludeId = 0): bool {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE nip = ? AND id != ?");
        $stmt->execute([$nip, $excludeId]);
        return $stmt->fetch() !== false;
    }

    public function emailExists(string $email, int $excludeId = 0): bool {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $excludeId]);
        return $stmt->fetch() !== false;
    }
}
