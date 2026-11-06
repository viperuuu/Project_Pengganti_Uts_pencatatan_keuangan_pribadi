<?php

declare(strict_types=1);

namespace UMM\Pemweb\Repository;

use PDO;
use UMM\Pemweb\Model\Dompet;

final class DompetRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Semua dompet beserta saldo sekarang:
     * saldo = saldo_awal + total pemasukan - total pengeluaran.
     */
    public function semuaDenganSaldo(): array
    {
        $sql = "SELECT d.id, d.nama, d.saldo_awal,
                       COUNT(t.id) AS jumlah_transaksi,
                       d.saldo_awal + COALESCE(SUM(
                           CASE k.tipe WHEN 'pemasukan' THEN t.nominal
                                       WHEN 'pengeluaran' THEN -t.nominal END
                       ), 0) AS saldo
                FROM dompet d
                LEFT JOIN transaksi t ON t.dompet_id = d.id
                LEFT JOIN kategori k ON k.id = t.kategori_id
                GROUP BY d.id, d.nama, d.saldo_awal
                ORDER BY d.nama";
        return $this->pdo->query($sql)->fetchAll();
    }

    /** @return Dompet[] */
    public function semuaModel(): array
    {
        $baris = $this->pdo->query('SELECT id, nama, saldo_awal FROM dompet ORDER BY nama')->fetchAll();
        return array_map(fn (array $b): Dompet => Dompet::dariBaris($b), $baris);
    }

    public function cari(int $id): ?Dompet
    {
        $stmt = $this->pdo->prepare('SELECT id, nama, saldo_awal FROM dompet WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $baris = $stmt->fetch();
        return $baris ? Dompet::dariBaris($baris) : null;
    }

    public function sudahAda(string $nama, ?int $kecualiId = null): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM dompet WHERE nama = :nama AND id <> :id');
        $stmt->execute(['nama' => $nama, 'id' => $kecualiId ?? 0]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function jumlahTransaksi(int $id): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM transaksi WHERE dompet_id = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn();
    }

    public function simpan(Dompet $d): void
    {
        if ($d->id === null) {
            $stmt = $this->pdo->prepare('INSERT INTO dompet (nama, saldo_awal) VALUES (:nama, :saldo)');
            $stmt->execute(['nama' => $d->nama, 'saldo' => $d->saldoAwal]);
            return;
        }
        $stmt = $this->pdo->prepare('UPDATE dompet SET nama = :nama, saldo_awal = :saldo WHERE id = :id');
        $stmt->execute(['nama' => $d->nama, 'saldo' => $d->saldoAwal, 'id' => $d->id]);
    }

    public function hapus(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM dompet WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
