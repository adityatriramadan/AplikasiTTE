<?php
class TemplateSuratModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function getAll(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM template_surat ORDER BY kode_jenis");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAktif(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM template_surat WHERE status = 'aktif' ORDER BY kode_jenis");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM template_surat WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function tambah(array $data): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO template_surat (kode_jenis, nama_jenis, konten_template, format_nomor, field_dinamis, status) VALUES (?,?,?,?,?,?)"
        );
        $stmt->execute([
            $data['kode_jenis'],
            $data['nama_jenis'],
            $data['konten_template'],
            $data['format_nomor'],
            $data['field_dinamis'],
            $data['status'] ?? 'aktif',
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE template_surat SET nama_jenis=?, konten_template=?, format_nomor=?, field_dinamis=?, status=? WHERE id=?"
        );
        return $stmt->execute([
            $data['nama_jenis'],
            $data['konten_template'],
            $data['format_nomor'],
            $data['field_dinamis'],
            $data['status'],
            $id,
        ]);
    }

    public function toggleStatus(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE template_surat SET status = IF(status='aktif','nonaktif','aktif') WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function kodeExists(string $kode, int $excludeId = 0): bool {
        $stmt = $this->pdo->prepare("SELECT id FROM template_surat WHERE kode_jenis = ? AND id != ?");
        $stmt->execute([$kode, $excludeId]);
        return $stmt->fetch() !== false;
    }
}
