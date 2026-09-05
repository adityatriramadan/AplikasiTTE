<?php
class TandaTanganModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function simpan(array $data): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO tanda_tangan (surat_id, kaprodi_id, hash_sha256, signature_rsa, public_key_snapshot, timestamp_tandatangan)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['surat_id'],
            $data['kaprodi_id'],
            $data['hash_sha256'],
            $data['signature_rsa'],
            $data['public_key_snapshot'],
            $data['timestamp_tandatangan'],
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updatePath(int $id, array $data): bool {
        $stmt = $this->pdo->prepare("UPDATE tanda_tangan SET pdf_path=?, qr_code_url=? WHERE id=?");
        return $stmt->execute([$data['pdf_path'], $data['qr_code_url'], $id]);
    }

    public function updateById(int $id, array $data): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE tanda_tangan
             SET kaprodi_id = ?, hash_sha256 = ?, signature_rsa = ?, public_key_snapshot = ?, timestamp_tandatangan = ?, pdf_path = ?, qr_code_url = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['kaprodi_id'],
            $data['hash_sha256'],
            $data['signature_rsa'],
            $data['public_key_snapshot'],
            $data['timestamp_tandatangan'],
            $data['pdf_path'],
            $data['qr_code_url'],
            $id,
        ]);
    }

    public function getBySuratId(int $suratId): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT tt.*, u.nama AS nama_kaprodi
             FROM tanda_tangan tt
             JOIN users u ON tt.kaprodi_id = u.id
             WHERE tt.surat_id = ?"
        );
        $stmt->execute([$suratId]);
        return $stmt->fetch();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM tanda_tangan WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getRiwayatKaprodi(int $kaprodiId, string $search = ''): array {
        $sql = "SELECT tt.*, s.nomor_surat, s.perihal, s.tanggal_surat, t.nama_jenis, u.nama AS nama_pembuat
                FROM tanda_tangan tt
                JOIN surat s ON tt.surat_id = s.id
                JOIN template_surat t ON s.template_id = t.id
                JOIN users u ON s.pembuat_id = u.id
                WHERE tt.kaprodi_id = ?";
        $params = [$kaprodiId];

        if ($search) {
            $sql .= " AND (s.nomor_surat LIKE ? OR s.perihal LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }
        $sql .= " ORDER BY tt.timestamp_tandatangan DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countThisMonth(int $kaprodiId): int {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM tanda_tangan 
             WHERE kaprodi_id = ? AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())"
        );
        $stmt->execute([$kaprodiId]);
        return (int)$stmt->fetchColumn();
    }
}
