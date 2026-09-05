<?php
class NomorSuratModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function getCounter(string $kodeJenis, int $tahun): int {
        $stmt = $this->pdo->prepare("SELECT counter FROM nomor_surat_counter WHERE kode_jenis = ? AND tahun = ?");
        $stmt->execute([$kodeJenis, $tahun]);
        return (int)($stmt->fetchColumn() ?: 0);
    }

    public function getAll(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM nomor_surat_counter ORDER BY tahun DESC, kode_jenis");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
