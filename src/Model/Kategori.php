<?php

declare(strict_types=1);

namespace UMM\Pemweb\Model;

final class Kategori
{
    public const TIPE = ['pemasukan', 'pengeluaran'];

    public function __construct(
        public readonly ?int $id,
        public readonly string $nama,
        public readonly string $tipe,
    ) {
    }

    public static function dariBaris(array $b): self
    {
        return new self((int) $b['id'], $b['nama'], $b['tipe']);
    }
}
