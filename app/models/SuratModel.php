<?php
class SuratModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function getAll(array $filter = []): array {
        $sql = "SELECT s.*, t.nama_jenis, t.kode_jenis, u.nama AS nama_pembuat 
                FROM surat s 
                JOIN template_surat t ON s.template_id = t.id 
                JOIN users u ON s.pembuat_id = u.id";
        $params = [];

        $where = [];
        if (!empty($filter['status'])) {
            $where[] = "s.status = ?";
            $params[] = $filter['status'];
        }
        if (!empty($filter['pembuat_id'])) {
            $where[] = "s.pembuat_id = ?";
            $params[] = $filter['pembuat_id'];
        }
        if (!empty($filter['search'])) {
            $where[] = "(s.nomor_surat LIKE ? OR s.perihal LIKE ?)";
            $params[] = '%' . $filter['search'] . '%';
            $params[] = '%' . $filter['search'] . '%';
        }
        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY s.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT s.*, t.nama_jenis, t.kode_jenis, t.konten_template, t.field_dinamis, u.nama AS nama_pembuat
             FROM surat s 
             JOIN template_surat t ON s.template_id = t.id
             JOIN users u ON s.pembuat_id = u.id
             WHERE s.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getSuratMenunggu(): array {
        $stmt = $this->pdo->prepare(
            "SELECT s.*, t.nama_jenis, u.nama AS nama_pembuat
             FROM surat s 
             JOIN template_surat t ON s.template_id = t.id
             JOIN users u ON s.pembuat_id = u.id
             WHERE s.status = 'menunggu'
             ORDER BY s.created_at ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSuratDitandatangani(): array {
        $stmt = $this->pdo->prepare(
            "SELECT s.*, t.nama_jenis, u.nama AS nama_pembuat, tt.timestamp_tandatangan, tt.pdf_path
             FROM surat s 
             JOIN template_surat t ON s.template_id = t.id
             JOIN users u ON s.pembuat_id = u.id
             LEFT JOIN tanda_tangan tt ON s.id = tt.surat_id
             WHERE s.status IN ('ditandatangani','didistribusikan')
             ORDER BY tt.timestamp_tandatangan DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function tambah(array $data): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO surat (nomor_surat, template_id, perihal, isi_data, pembuat_id, penerima_nama, penerima_jabatan, penerima_instansi, status, tanggal_surat)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['nomor_surat'],
            $data['template_id'],
            $data['perihal'],
            $data['isi_data'],
            $data['pembuat_id'],
            $data['penerima_nama'] ?? null,
            $data['penerima_jabatan'] ?? null,
            $data['penerima_instansi'] ?? null,
            $data['status'] ?? 'draft',
            $data['tanggal_surat'],
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->pdo->prepare("UPDATE surat SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function tolak(int $id, string $alasan): bool {
        $stmt = $this->pdo->prepare("UPDATE surat SET status = 'ditolak', alasan_tolak = ? WHERE id = ?");
        return $stmt->execute([$alasan, $id]);
    }

    public function countByStatus(): array {
        $stmt = $this->pdo->prepare("SELECT status, COUNT(*) as total FROM surat GROUP BY status");
        $stmt->execute();
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['status']] = (int)$row['total'];
        }
        return $result;
    }

    public function countMenunggu(): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM surat WHERE status = 'menunggu'");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getForDosen(int $dosenId): array {
        $stmt = $this->pdo->prepare(
            "SELECT s.*, t.nama_jenis, u.nama AS nama_pembuat, d.status_baca
             FROM distribusi d
             JOIN surat s ON d.surat_id = s.id
             JOIN template_surat t ON s.template_id = t.id
             JOIN users u ON s.pembuat_id = u.id
             WHERE d.penerima_id = ?
             ORDER BY d.tanggal_kirim DESC"
        );
        $stmt->execute([$dosenId]);
        return $stmt->fetchAll();
    }
}
