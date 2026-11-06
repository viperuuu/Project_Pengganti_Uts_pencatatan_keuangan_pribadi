<?php

declare(strict_types=1);

namespace UMM\Pemweb\Model;

final class Transaksi
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $kategoriId,
        public readonly int $dompetId,
        public readonly string $nominal,
        public readonly string $tanggal,
        public readonly ?string $keterangan,
    ) {
    }

    public static function dariBaris(array $b): self
    {
        return new self(
            (int) $b['id'],
            (int) $b['kategori_id'],
            (int) $b['dompet_id'],
            (string) $b['nominal'],
            $b['tanggal'],
            $b['keterangan'],
        );
    }
}
