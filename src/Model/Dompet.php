<?php

declare(strict_types=1);

namespace UMM\Pemweb\Model;

final class Dompet
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $nama,
        public readonly string $saldoAwal,
    ) {
    }

    public static function dariBaris(array $b): self
    {
        return new self((int) $b['id'], $b['nama'], (string) $b['saldo_awal']);
    }
}
