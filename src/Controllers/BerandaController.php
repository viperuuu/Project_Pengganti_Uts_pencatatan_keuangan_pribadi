<?php

declare(strict_types=1);

namespace UMM\Pemweb\Controllers;

use Throwable;
use UMM\Pemweb\Core\Controller;
use UMM\Pemweb\Core\Database;
use UMM\Pemweb\Repository\DompetRepository;
use UMM\Pemweb\Repository\TransaksiRepository;
use UMM\Pemweb\Service\LaporanService;

class BerandaController extends Controller
{
    public function index(): void
    {
        try {
            $pdo = Database::koneksi();
            $laporan = new LaporanService(new DompetRepository($pdo), new TransaksiRepository($pdo));
            $ringkasan = $laporan->ringkasanBeranda(date('Y-m'));
            $galat = null;
        } catch (Throwable $e) {
            $ringkasan = null;
            $galat = 'Belum terhubung ke database: ' . $e->getMessage();
        }

        $this->view('beranda/index', [
            'judul'     => 'Beranda',
            'ringkasan' => $ringkasan,
            'galat'     => $galat,
        ]);
    }
}
