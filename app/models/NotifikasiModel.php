<?php
class NotifikasiModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function getByUserId(int $userId, int $limit = 20): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM notifikasi WHERE user_id = ? ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function countBelumDibaca(int $userId): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM notifikasi WHERE user_id = ? AND is_dibaca = 0");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public function kirim(int $userId, string $pesan, string $url = ''): int {
        $stmt = $this->pdo->prepare("INSERT INTO notifikasi (user_id, pesan, url) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $pesan, $url]);
        return (int)$this->pdo->lastInsertId();
    }

    public function tandaiDibaca(int $id, int $userId): bool {
        $stmt = $this->pdo->prepare("UPDATE notifikasi SET is_dibaca = 1 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    public function tandaiSemuaDibaca(int $userId): bool {
        $stmt = $this->pdo->prepare("UPDATE notifikasi SET is_dibaca = 1 WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
