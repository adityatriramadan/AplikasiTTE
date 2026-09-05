<?php
class KunciRSAModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function getAktifByUserId(int $userId): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM kunci_rsa WHERE user_id = ? AND is_aktif = 1 ORDER BY created_at DESC LIMIT 1"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getByUserId(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM kunci_rsa WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAllWithUser(): array {
        $stmt = $this->pdo->prepare(
            "SELECT k.*, u.nama, u.nip, u.jabatan
             FROM kunci_rsa k
             JOIN users u ON k.user_id = u.id
             ORDER BY k.created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function simpan(array $data): int {
        // Nonaktifkan kunci lama terlebih dahulu
        $stmt = $this->pdo->prepare("UPDATE kunci_rsa SET is_aktif = 0 WHERE user_id = ?");
        $stmt->execute([$data['user_id']]);

        // Simpan kunci baru
        $stmt = $this->pdo->prepare(
            "INSERT INTO kunci_rsa (user_id, public_key, private_key_encrypted, is_aktif) VALUES (?, ?, ?, 1)"
        );
        $stmt->execute([
            $data['user_id'],
            $data['public_key'],
            $data['private_key_encrypted'],
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function nonaktifkan(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE kunci_rsa SET is_aktif = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function hasAktifKey(int $userId): bool {
        $stmt = $this->pdo->prepare("SELECT id FROM kunci_rsa WHERE user_id = ? AND is_aktif = 1");
        $stmt->execute([$userId]);
        return $stmt->fetch() !== false;
    }
}
