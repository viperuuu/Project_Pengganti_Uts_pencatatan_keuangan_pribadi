<?php

declare(strict_types=1);

namespace UMM\Pemweb\Controllers;

use UMM\Pemweb\Core\Controller;
use UMM\Pemweb\Core\Database;
use UMM\Pemweb\Core\Flash;
use UMM\Pemweb\Model\Dompet;
use UMM\Pemweb\Repository\DompetRepository;

class DompetController extends Controller
{
    private DompetRepository $repo;

    public function __construct()
    {
        $this->repo = new DompetRepository(Database::koneksi());
    }

    public function index(): void
    {
        $this->view('dompet/index', ['judul' => 'Dompet', 'daftar' => $this->repo->semuaDenganSaldo()]);
    }

    public function tambah(): void
    {
        $this->view('dompet/form', $this->dataForm('Tambah dompet', '/dompet', null, ['nama' => '', 'saldo_awal' => '0'], []));
    }

    public function simpan(): void
    {
        $this->cekCsrf();
        $input = $this->ambilInput();
        $galat = $this->validasi($input, null);

        if ($galat !== []) {
            $this->formGagal('dompet/form', $this->dataForm('Tambah dompet', '/dompet', null, $input, $galat));
            return;
        }

        $this->repo->simpan(new Dompet(null, $input['nama'], $input['saldo_awal']));
        Flash::set('sukses', 'Dompet berhasil ditambahkan.');
        $this->redirect('/dompet');
    }

    public function ubah(): void
    {
        $d = $this->repo->cari($this->idDariGet()) ?? $this->tidakDitemukan('Dompet tidak ditemukan.');
        $this->view('dompet/form', $this->dataForm('Ubah dompet', '/dompet/ubah', $d->id, ['nama' => $d->nama, 'saldo_awal' => $d->saldoAwal], []));
    }

    public function perbarui(): void
    {
        $this->cekCsrf();
        $lama = $this->repo->cari($this->idDariPost()) ?? $this->tidakDitemukan('Dompet tidak ditemukan.');
        $input = $this->ambilInput();
        $galat = $this->validasi($input, $lama);

        if ($galat !== []) {
            $this->formGagal('dompet/form', $this->dataForm('Ubah dompet', '/dompet/ubah', $lama->id, $input, $galat));
            return;
        }

        $this->repo->simpan(new Dompet($lama->id, $input['nama'], $input['saldo_awal']));
        Flash::set('sukses', 'Dompet berhasil diperbarui.');
        $this->redirect('/dompet');
    }

    public function hapus(): void
    {
        $this->cekCsrf();
        $d = $this->repo->cari($this->idDariPost()) ?? $this->tidakDitemukan('Dompet tidak ditemukan.');

        if ($this->repo->jumlahTransaksi($d->id) > 0) {
            Flash::set('galat', 'Dompet "' . $d->nama . '" tidak bisa dihapus karena masih punya transaksi.');
            $this->redirect('/dompet');
        }

        $this->repo->hapus($d->id);
        Flash::set('sukses', 'Dompet berhasil dihapus.');
        $this->redirect('/dompet');
    }

    /** @return array{nama: string, saldo_awal: string} */
    private function ambilInput(): array
    {
        return [
            'nama'       => $this->post('nama'),
            'saldo_awal' => str_replace(',', '.', $this->post('saldo_awal', '0')),
        ];
    }

    /** @return array<string, string> */
    private function validasi(array $in, ?Dompet $lama): array
    {
        $galat = [];
        $panjang = panjang_teks($in['nama']);
        if ($panjang === 0) {
            $galat['nama'] = 'Nama dompet wajib diisi.';
        } elseif ($panjang > 50) {
            $galat['nama'] = 'Nama dompet maksimal 50 karakter.';
        } elseif ($this->repo->sudahAda($in['nama'], $lama?->id)) {
            $galat['nama'] = 'Nama dompet itu sudah dipakai.';
        }

        if (!preg_match('/^\d{1,13}(\.\d{1,2})?$/', $in['saldo_awal'])) {
            $galat['saldo_awal'] = 'Saldo awal harus berupa angka nol atau lebih (tanpa titik ribuan).';
        }
        return $galat;
    }

    private function dataForm(string $judul, string $aksi, ?int $id, array $data, array $galat): array
    {
        return ['judul' => $judul, 'aksi' => $aksi, 'id' => $id, 'data' => $data, 'galat' => $galat];
    }
}
