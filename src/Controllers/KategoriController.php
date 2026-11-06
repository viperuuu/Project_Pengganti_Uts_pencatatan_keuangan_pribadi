<?php

declare(strict_types=1);

namespace UMM\Pemweb\Controllers;

use UMM\Pemweb\Core\Controller;
use UMM\Pemweb\Core\Database;
use UMM\Pemweb\Core\Flash;
use UMM\Pemweb\Model\Kategori;
use UMM\Pemweb\Repository\KategoriRepository;

class KategoriController extends Controller
{
    private KategoriRepository $repo;

    public function __construct()
    {
        $this->repo = new KategoriRepository(Database::koneksi());
    }

    public function index(): void
    {
        $this->view('kategori/index', ['judul' => 'Kategori', 'daftar' => $this->repo->semua()]);
    }

    public function tambah(): void
    {
        $this->view('kategori/form', $this->dataForm('Tambah kategori', '/kategori', null, ['nama' => '', 'tipe' => 'pengeluaran'], []));
    }

    public function simpan(): void
    {
        $this->cekCsrf();
        $input = $this->ambilInput();
        $galat = $this->validasi($input, null);

        if ($galat !== []) {
            $this->formGagal('kategori/form', $this->dataForm('Tambah kategori', '/kategori', null, $input, $galat));
            return;
        }

        $this->repo->simpan(new Kategori(null, $input['nama'], $input['tipe']));
        Flash::set('sukses', 'Kategori berhasil ditambahkan.');
        $this->redirect('/kategori');
    }

    public function ubah(): void
    {
        $k = $this->repo->cari($this->idDariGet()) ?? $this->tidakDitemukan('Kategori tidak ditemukan.');
        $this->view('kategori/form', $this->dataForm('Ubah kategori', '/kategori/ubah', $k->id, ['nama' => $k->nama, 'tipe' => $k->tipe], []));
    }

    public function perbarui(): void
    {
        $this->cekCsrf();
        $lama = $this->repo->cari($this->idDariPost()) ?? $this->tidakDitemukan('Kategori tidak ditemukan.');
        $input = $this->ambilInput();
        $galat = $this->validasi($input, $lama);

        if ($galat !== []) {
            $this->formGagal('kategori/form', $this->dataForm('Ubah kategori', '/kategori/ubah', $lama->id, $input, $galat));
            return;
        }

        $this->repo->simpan(new Kategori($lama->id, $input['nama'], $input['tipe']));
        Flash::set('sukses', 'Kategori berhasil diperbarui.');
        $this->redirect('/kategori');
    }

    public function hapus(): void
    {
        $this->cekCsrf();
        $k = $this->repo->cari($this->idDariPost()) ?? $this->tidakDitemukan('Kategori tidak ditemukan.');

        if ($this->repo->jumlahTransaksi($k->id) > 0) {
            Flash::set('galat', 'Kategori "' . $k->nama . '" tidak bisa dihapus karena masih dipakai transaksi.');
            $this->redirect('/kategori');
        }

        $this->repo->hapus($k->id);
        Flash::set('sukses', 'Kategori berhasil dihapus.');
        $this->redirect('/kategori');
    }

    /** @return array{nama: string, tipe: string} */
    private function ambilInput(): array
    {
        return ['nama' => $this->post('nama'), 'tipe' => $this->post('tipe')];
    }

    /** @return array<string, string> pesan galat per kolom (kosong = valid) */
    private function validasi(array $in, ?Kategori $lama): array
    {
        $galat = [];
        $panjang = panjang_teks($in['nama']);
        if ($panjang === 0) {
            $galat['nama'] = 'Nama kategori wajib diisi.';
        } elseif ($panjang > 50) {
            $galat['nama'] = 'Nama kategori maksimal 50 karakter.';
        }

        if (!in_array($in['tipe'], Kategori::TIPE, true)) {
            $galat['tipe'] = 'Pilih tipe kategori.';
        }

        if ($galat === [] && $this->repo->sudahAda($in['nama'], $in['tipe'], $lama?->id)) {
            $galat['nama'] = 'Kategori dengan nama dan tipe itu sudah ada.';
        }

        if (
            $lama !== null && !isset($galat['tipe']) && $in['tipe'] !== $lama->tipe
            && $this->repo->jumlahTransaksi((int) $lama->id) > 0
        ) {
            $galat['tipe'] = 'Tipe tidak bisa diubah karena kategori ini sudah dipakai transaksi.';
        }
        return $galat;
    }

    private function dataForm(string $judul, string $aksi, ?int $id, array $data, array $galat): array
    {
        return ['judul' => $judul, 'aksi' => $aksi, 'id' => $id, 'data' => $data, 'galat' => $galat];
    }
}
