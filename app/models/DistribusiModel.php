<?php
class DistribusiModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function kirim(int $suratId, ?int $penerimaId, string $penerimaEksternal = ''): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO distribusi (surat_id, penerima_id, penerima_eksternal) VALUES (?, ?, ?)"
        );
        $stmt->execute([$suratId, $penerimaId, $penerimaEksternal ?: null]);
        return (int)$this->pdo->lastInsertId();
    }

    public function getBySuratId(int $suratId): array {
        $stmt = $this->pdo->prepare(
            "SELECT d.*, u.nama AS nama_penerima_internal
             FROM distribusi d
             LEFT JOIN users u ON d.penerima_id = u.id
             WHERE d.surat_id = ?
             ORDER BY d.tanggal_kirim DESC"
        );
        $stmt->execute([$suratId]);
        return $stmt->fetchAll();
    }

    public function tandaiDibaca(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE distribusi SET status_baca='dibaca', dibaca_pada=NOW() WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function countBelumDibaca(int $penerimaId): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM distribusi WHERE penerima_id = ? AND status_baca = 'belum'");
        $stmt->execute([$penerimaId]);
        return (int)$stmt->fetchColumn();
    }
}
