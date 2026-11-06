<?php

declare(strict_types=1);

namespace UMM\Pemweb\Service;

use UMM\Pemweb\Repository\DompetRepository;
use UMM\Pemweb\Repository\TransaksiRepository;

/** Logika rekap untuk beranda — murni data, tidak tahu HTML. */
final class LaporanService
{
    public function __construct(
        private DompetRepository $dompet,
        private TransaksiRepository $transaksi,
    ) {
    }

    public function ringkasanBeranda(string $bulan): array
    {
        $daftarDompet = $this->dompet->semuaDenganSaldo();
        $saldoTotal = array_sum(array_map(fn (array $d): float => (float) $d['saldo'], $daftarDompet));
        $totalBulan = $this->transaksi->total($bulan);

        return [
            'bulan'       => $bulan,
            'saldo_total' => $saldoTotal,
            'pemasukan'   => $totalBulan['pemasukan'],
            'pengeluaran' => $totalBulan['pengeluaran'],
            'dompet'      => $daftarDompet,
            'terbaru'     => $this->transaksi->terbaru(5),
        ];
    }
}
