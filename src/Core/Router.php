<?php

declare(strict_types=1);

namespace UMM\Pemweb\Core;

class Router
{
    /** @var array<string, array<string, array|callable>> */
    private array $rute = [];

    public function get(string $path, array|callable $aksi): void
    {
        $this->rute['GET'][$path] = $aksi;
    }

    public function post(string $path, array|callable $aksi): void
    {
        $this->rute['POST'][$path] = $aksi;
    }

    public function dispatch(string $method, string $path): void
    {
        $aksi = $this->rute[$method][$path] ?? null;

        if ($aksi === null) {
            // Path dikenal tapi method salah => 405, selain itu => 404.
            $adaDiMethodLain = false;
            foreach ($this->rute as $daftar) {
                if (isset($daftar[$path])) {
                    $adaDiMethodLain = true;
                    break;
                }
            }
            if ($adaDiMethodLain) {
                tampilkan_error(405, 'Metode tidak diizinkan', 'Alamat ini ada, tetapi tidak menerima metode permintaan tersebut.');
            } else {
                tampilkan_error(404, 'Halaman tidak ditemukan', 'Alamat yang kamu buka tidak terdaftar di aplikasi.');
            }
            return;
        }

        if (is_callable($aksi)) {
            $aksi();
            return;
        }

        [$kelas, $namaMethod] = $aksi;
        (new $kelas())->$namaMethod();
    }
}
