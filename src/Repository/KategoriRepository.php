<?php

declare(strict_types=1);

namespace UMM\Pemweb\Repository;

use PDO;
use UMM\Pemweb\Model\Kategori;

final class KategoriRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /** Semua kategori + jumlah transaksi yang memakainya (untuk tabel daftar). */
    public function semua(): array
    {
        $sql = 'SELECT k.id, k.nama, k.tipe, COUNT(t.id) AS jumlah_transaksi
                FROM kategori k
                LEFT JOIN transaksi t ON t.kategori_id = k.id
                GROUP BY k.id, k.nama, k.tipe
                ORDER BY k.tipe, k.nama';
        return $this->pdo->query($sql)->fetchAll();
    }

    /** @return Kategori[] */
    public function semuaModel(): array
    {
        $baris = $this->pdo->query('SELECT id, nama, tipe FROM kategori ORDER BY tipe, nama')->fetchAll();
        return array_map(fn (array $b): Kategori => Kategori::dariBaris($b), $baris);
    }

    public function cari(int $id): ?Kategori
    {
        $stmt = $this->pdo->prepare('SELECT id, nama, tipe FROM kategori WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $baris = $stmt->fetch();
        return $baris ? Kategori::dariBaris($baris) : null;
    }

    public function sudahAda(string $nama, string $tipe, ?int $kecualiId = null): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM kategori WHERE nama = :nama AND tipe = :tipe AND id <> :id');
        $stmt->execute(['nama' => $nama, 'tipe' => $tipe, 'id' => $kecualiId ?? 0]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function jumlahTransaksi(int $id): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM transaksi WHERE kategori_id = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn();
    }

    /** INSERT bila id null, UPDATE bila id ada. */
    public function simpan(Kategori $k): void
    {
        if ($k->id === null) {
            $stmt = $this->pdo->prepare('INSERT INTO kategori (nama, tipe) VALUES (:nama, :tipe)');
            $stmt->execute(['nama' => $k->nama, 'tipe' => $k->tipe]);
            return;
        }
        $stmt = $this->pdo->prepare('UPDATE kategori SET nama = :nama, tipe = :tipe WHERE id = :id');
        $stmt->execute(['nama' => $k->nama, 'tipe' => $k->tipe, 'id' => $k->id]);
    }

    public function hapus(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM kategori WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
