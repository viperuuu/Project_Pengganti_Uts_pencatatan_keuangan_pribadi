<?php

declare(strict_types=1);

namespace UMM\Pemweb\Core;

/**
 * Kelas dasar controller. Controller TIPIS: terima request, validasi, minta data ke
 * repository/service, pilih view. Tidak ada SQL dan tidak ada HTML panjang di sini.
 */
abstract class Controller
{
    /**
     * Tampilkan view dari folder views/. Contoh: $this->view('kategori/index', [...])
     * Nama variabel lokal sengaja diawali "_" agar tidak bentrok dengan kunci data
     * (mis. kunci "data" yang dipakai form).
     */
    protected function view(string $_nama, array $_data = []): void
    {
        $_berkas = __DIR__ . '/../../views/' . $_nama . '.php';
        if (!is_file($_berkas)) {
            throw new \RuntimeException("View tidak ditemukan: {$_nama}");
        }
        extract($_data, EXTR_SKIP);
        require $_berkas;
    }

    /** Validasi gagal: tampilkan ulang form dengan status 422 (data lama + pesan error). */
    protected function formGagal(string $nama, array $data): void
    {
        http_response_code(422);
        $this->view($nama, $data);
    }

    /** Pola PRG: setelah POST, arahkan ke GET supaya refresh tidak menggandakan data. */
    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function post(string $kunci, string $bawaan = ''): string
    {
        return trim((string) ($_POST[$kunci] ?? $bawaan));
    }

    protected function get(string $kunci, string $bawaan = ''): string
    {
        return trim((string) ($_GET[$kunci] ?? $bawaan));
    }

    /** Ambil id positif dari query string (?id=3); 0 bila tidak valid. */
    protected function idDariGet(): int
    {
        return $this->angkaPositif($_GET['id'] ?? null);
    }

    /** Ambil id positif dari body POST; 0 bila tidak valid. */
    protected function idDariPost(): int
    {
        return $this->angkaPositif($_POST['id'] ?? null);
    }

    /** Wajib dipanggil di awal setiap aksi POST. */
    protected function cekCsrf(): void
    {
        $token = (string) ($_POST['_csrf'] ?? '');
        if (!hash_equals(csrf_token(), $token)) {
            tampilkan_error(403, 'Permintaan ditolak', 'Sesi formulir tidak valid atau sudah kedaluwarsa. Muat ulang halaman lalu coba lagi.');
            exit;
        }
    }

    protected function tidakDitemukan(string $pesan = 'Data yang kamu cari tidak ada.'): never
    {
        tampilkan_error(404, 'Data tidak ditemukan', $pesan);
        exit;
    }

    private function angkaPositif(mixed $nilai): int
    {
        $id = filter_var($nilai, FILTER_VALIDATE_INT);
        return ($id !== false && $id > 0) ? $id : 0;
    }
}
