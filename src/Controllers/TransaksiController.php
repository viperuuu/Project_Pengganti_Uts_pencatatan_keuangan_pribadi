<?php

declare(strict_types=1);

namespace UMM\Pemweb\Controllers;

use DateTimeImmutable;
use UMM\Pemweb\Core\Controller;
use UMM\Pemweb\Core\Database;
use UMM\Pemweb\Core\Flash;
use UMM\Pemweb\Model\Transaksi;
use UMM\Pemweb\Repository\DompetRepository;
use UMM\Pemweb\Repository\KategoriRepository;
use UMM\Pemweb\Repository\TransaksiRepository;

class TransaksiController extends Controller
{
    private TransaksiRepository $repo;
    private KategoriRepository $kategori;
    private DompetRepository $dompet;

    public function __construct()
    {
        $pdo = Database::koneksi();
        $this->repo = new TransaksiRepository($pdo);
        $this->kategori = new KategoriRepository($pdo);
        $this->dompet = new DompetRepository($pdo);
    }

    public function index(): void
    {
        $bulan = $this->get('bulan');
        $bulan = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $bulan) ? $bulan : null;

        $this->view('transaksi/index', [
            'judul'  => 'Transaksi',
            'bulan'  => $bulan,
            'daftar' => $this->repo->semua($bulan),
            'total'  => $this->repo->total($bulan),
        ]);
    }

    public function tambah(): void
    {
        $bawaan = ['kategori_id' => '', 'dompet_id' => '', 'nominal' => '', 'tanggal' => date('Y-m-d'), 'keterangan' => ''];
        $this->view('transaksi/form', $this->dataForm('Tambah transaksi', '/transaksi', null, $bawaan, []));
    }

    public function simpan(): void
    {
        $this->cekCsrf();
        $input = $this->ambilInput();
        $galat = $this->validasi($input);

        if ($galat !== []) {
            $this->formGagal('transaksi/form', $this->dataForm('Tambah transaksi', '/transaksi', null, $input, $galat));
            return;
        }

        $this->repo->simpan($this->bentukModel(null, $input));
        Flash::set('sukses', 'Transaksi berhasil ditambahkan.');
        $this->redirect('/transaksi');
    }

    public function ubah(): void
    {
        $t = $this->repo->cari($this->idDariGet()) ?? $this->tidakDitemukan('Transaksi tidak ditemukan.');
        $data = [
            'kategori_id' => (string) $t->kategoriId,
            'dompet_id'   => (string) $t->dompetId,
            'nominal'     => $t->nominal,
            'tanggal'     => $t->tanggal,
            'keterangan'  => (string) $t->keterangan,
        ];
        $this->view('transaksi/form', $this->dataForm('Ubah transaksi', '/transaksi/ubah', $t->id, $data, []));
    }

    public function perbarui(): void
    {
        $this->cekCsrf();
        $lama = $this->repo->cari($this->idDariPost()) ?? $this->tidakDitemukan('Transaksi tidak ditemukan.');
        $input = $this->ambilInput();
        $galat = $this->validasi($input);

        if ($galat !== []) {
            $this->formGagal('transaksi/form', $this->dataForm('Ubah transaksi', '/transaksi/ubah', $lama->id, $input, $galat));
            return;
        }

        $this->repo->simpan($this->bentukModel($lama->id, $input));
        Flash::set('sukses', 'Transaksi berhasil diperbarui.');
        $this->redirect('/transaksi');
    }

    public function hapus(): void
    {
        $this->cekCsrf();
        $t = $this->repo->cari($this->idDariPost()) ?? $this->tidakDitemukan('Transaksi tidak ditemukan.');
        $this->repo->hapus((int) $t->id);
        Flash::set('sukses', 'Transaksi berhasil dihapus.');
        $this->redirect('/transaksi');
    }

    private function ambilInput(): array
    {
        return [
            'kategori_id' => $this->post('kategori_id'),
            'dompet_id'   => $this->post('dompet_id'),
            'nominal'     => str_replace(',', '.', $this->post('nominal')),
            'tanggal'     => $this->post('tanggal'),
            'keterangan'  => $this->post('keterangan'),
        ];
    }

    /** @return array<string, string> */
    private function validasi(array $in): array
    {
        $galat = [];

        $kategoriId = filter_var($in['kategori_id'], FILTER_VALIDATE_INT);
        if ($kategoriId === false || $this->kategori->cari($kategoriId) === null) {
            $galat['kategori_id'] = 'Pilih kategori yang tersedia.';
        }

        $dompetId = filter_var($in['dompet_id'], FILTER_VALIDATE_INT);
        if ($dompetId === false || $this->dompet->cari($dompetId) === null) {
            $galat['dompet_id'] = 'Pilih dompet yang tersedia.';
        }

        if (!preg_match('/^\d{1,13}(\.\d{1,2})?$/', $in['nominal']) || (float) $in['nominal'] <= 0) {
            $galat['nominal'] = 'Nominal harus berupa angka lebih dari nol (tanpa titik ribuan).';
        }

        $tanggal = DateTimeImmutable::createFromFormat('Y-m-d', $in['tanggal']);
        if ($tanggal === false || $tanggal->format('Y-m-d') !== $in['tanggal']) {
            $galat['tanggal'] = 'Tanggal tidak valid.';
        }

        if (panjang_teks($in['keterangan']) > 255) {
            $galat['keterangan'] = 'Keterangan maksimal 255 karakter.';
        }
        return $galat;
    }

    private function bentukModel(?int $id, array $in): Transaksi
    {
        return new Transaksi(
            $id,
            (int) $in['kategori_id'],
            (int) $in['dompet_id'],
            $in['nominal'],
            $in['tanggal'],
            $in['keterangan'] === '' ? null : $in['keterangan'],
        );
    }

    private function dataForm(string $judul, string $aksi, ?int $id, array $data, array $galat): array
    {
        return [
            'judul'        => $judul,
            'aksi'         => $aksi,
            'id'           => $id,
            'data'         => $data,
            'galat'        => $galat,
            'daftarKategori' => $this->kategori->semuaModel(),
            'daftarDompet'   => $this->dompet->semuaModel(),
        ];
    }
}
