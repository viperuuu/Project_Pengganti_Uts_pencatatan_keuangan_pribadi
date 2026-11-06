<?php

declare(strict_types=1);

namespace UMM\Pemweb\Core;

/** Pesan sekali tampil (disimpan di session) — dipakai setelah redirect PRG. */
class Flash
{
    public static function set(string $tipe, string $pesan): void
    {
        $_SESSION['_flash'][] = ['tipe' => $tipe, 'pesan' => $pesan];
    }

    /** @return array<int, array{tipe: string, pesan: string}> */
    public static function ambil(): array
    {
        $pesan = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $pesan;
    }
}
