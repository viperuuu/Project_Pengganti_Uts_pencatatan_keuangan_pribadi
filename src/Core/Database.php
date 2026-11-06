<?php

declare(strict_types=1);

namespace UMM\Pemweb\Core;

use PDO;

/** Menyimpan SATU koneksi PDO yang dipakai ulang oleh semua repository. */
class Database
{
    private static ?PDO $koneksi = null;

    public static function koneksi(): PDO
    {
        if (self::$koneksi === null) {
            self::$koneksi = require __DIR__ . '/../config/koneksi.php';
        }
        return self::$koneksi;
    }
}
