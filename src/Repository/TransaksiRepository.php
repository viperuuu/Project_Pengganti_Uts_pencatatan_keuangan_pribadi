<?php

declare(strict_types=1);

namespace UMM\Pemweb\Repository;

use DateTimeImmutable;
use PDO;
use UMM\Pemweb\Model\Transaksi;

final class TransaksiRepository
{
    private const SELECT_GABUNGAN = 'SELECT t.id, t.nominal, t.tanggal, t.keterangan,
                                            k.nama AS kategori, k.tipe, d.nama AS dompet
                                     FROM transaksi t
                                     JOIN kategori k ON k.id = t.kategori_id
                                     JOIN dompet d ON d.id = t.dompet_id';

    public function __construct(private PDO $pdo)
    {
    }

    /** Daftar transaksi (terbaru dulu). $bulan berformat "YYYY-MM"; null = semua. */
    public function semua(?string $bulan = null): array
    {
        $sql = self::SELECT_GABUNGAN;
        $param = [];
        if ($bulan !== null) {
            [$awal, $akhir] = $this->rentang($bulan);
            $sql .= ' WHERE t.tanggal >= :awal AND t.tanggal < :akhir';
            $param = ['awal' => $awal, 'akhir' => $akhir];
        }
        $sql .= ' ORDER BY t.tanggal DESC, t.id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($param);
        return $stmt->fetchAll();
    }

    public function terbaru(int $jumlah): array
    {
        $sql = self::SELECT_GABUNGAN . ' ORDER BY t.tanggal DESC, t.id DESC LIMIT ' . max(1, $jumlah);
        return $this->pdo->query($sql)->fetchAll();
    }

    /** Total pemasukan & pengeluaran. @return array{pemasukan: float, pengeluaran: float} */
    public function total(?string $bulan = null): array
    {
        $sql = 'SELECT k.tipe, SUM(t.nominal) AS total
                FROM transaksi t JOIN kategori k ON k.id = t.kategori_id';
        $param = [];
        if ($bulan !== null) {
            [$awal, $akhir] = $this->rentang($bulan);
            $sql .= ' WHERE t.tanggal >= :awal AND t.tanggal < :akhir';
            $param = ['awal' => $awal, 'akhir' => $akhir];
        }
        $sql .= ' GROUP BY k.tipe';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($param);

        $hasil = ['pemasukan' => 0.0, 'pengeluaran' => 0.0];
        foreach ($stmt->fetchAll() as $baris) {
            $hasil[$baris['tipe']] = (float) $baris['total'];
        }
        return $hasil;
    }

    public function cari(int $id): ?Transaksi
    {
        $stmt = $this->pdo->prepare('SELECT id, kategori_id, dompet_id, nominal, tanggal, keterangan FROM transaksi WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $baris = $stmt->fetch();
        return $baris ? Transaksi::dariBaris($baris) : null;
    }

    public function simpan(Transaksi $t): void
    {
        $data = [
            'kategori'   => $t->kategoriId,
            'dompet'     => $t->dompetId,
            'nominal'    => $t->nominal,
            'tanggal'    => $t->tanggal,
            'keterangan' => $t->keterangan,
        ];
        if ($t->id === null) {
            $stmt = $this->pdo->prepare('INSERT INTO transaksi (kategori_id, dompet_id, nominal, tanggal, keterangan)
                                         VALUES (:kategori, :dompet, :nominal, :tanggal, :keterangan)');
            $stmt->execute($data);
            return;
        }
        $stmt = $this->pdo->prepare('UPDATE transaksi
                                     SET kategori_id = :kategori, dompet_id = :dompet, nominal = :nominal,
                                         tanggal = :tanggal, keterangan = :keterangan
                                     WHERE id = :id');
        $stmt->execute($data + ['id' => $t->id]);
    }

    public function hapus(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM transaksi WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /** @return array{0: string, 1: string} tanggal awal (inklusif) dan akhir (eksklusif) bulan itu */
    private function rentang(string $bulan): array
    {
        $awal = new DateTimeImmutable($bulan . '-01');
        return [$awal->format('Y-m-d'), $awal->modify('first day of next month')->format('Y-m-d')];
    }
}
