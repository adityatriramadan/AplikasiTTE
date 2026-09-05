<?php
class LogAktivitasModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function catat(int $userId, string $aksi, string $keterangan = ''): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO log_aktivitas (user_id, aksi, keterangan, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$userId, $aksi, $keterangan, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0']);
    }

    public function getAll(array $filter = []): array {
        $sql = "SELECT l.*, u.nama, u.role FROM log_aktivitas l JOIN users u ON l.user_id = u.id";
        $params = [];
        $where = [];

        if (!empty($filter['user_id'])) {
            $where[] = "l.user_id = ?";
            $params[] = $filter['user_id'];
        }
        if (!empty($filter['aksi'])) {
            $where[] = "l.aksi = ?";
            $params[] = $filter['aksi'];
        }
        if (!empty($filter['search'])) {
            $where[] = "(u.nama LIKE ? OR l.keterangan LIKE ?)";
            $params[] = '%' . $filter['search'] . '%';
            $params[] = '%' . $filter['search'] . '%';
        }
        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY l.created_at DESC LIMIT 500";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countToday(): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM log_aktivitas WHERE DATE(created_at) = CURDATE()");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}
