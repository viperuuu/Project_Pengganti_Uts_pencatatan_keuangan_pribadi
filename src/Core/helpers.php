<?php
// Fungsi bantu global yang dipakai di View dan Controller.

declare(strict_types=1);

if (!function_exists('e')) {
    /** Escape output HTML (cegah XSS). Pakai di SETIAP data yang ditampilkan di view. */
    function e(?string $teks): string
    {
        return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('url')) {
    /** Bangun URL dengan memperhitungkan base path. Contoh: url('/kategori') */
    function url(string $path = '/'): string
    {
        return (defined('BASE_URL') ? BASE_URL : '') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('rupiah')) {
    /** Format angka ke rupiah. Contoh: rupiah(1500000) => "Rp 1.500.000" */
    function rupiah(float|int|string $angka): string
    {
        $angka = (float) $angka;
        $teks = number_format(abs($angka), 0, ',', '.');
        return ($angka < 0 ? '-Rp ' : 'Rp ') . $teks;
    }
}

if (!function_exists('tanggal_id')) {
    /** Ubah 2026-11-08 menjadi 08/11/2026. */
    function tanggal_id(string $ymd): string
    {
        $waktu = strtotime($ymd);
        return $waktu === false ? $ymd : date('d/m/Y', $waktu);
    }
}

if (!function_exists('csrf_token')) {
    /** Token anti-CSRF per sesi. */
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }
}

if (!function_exists('csrf_field')) {
    /** Input hidden berisi token; wajib ada di setiap form POST. */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('tampilkan_error')) {
    /** Render halaman error dengan kode status HTTP. Variabel lokal dibaca oleh view. */
    function tampilkan_error(int $kode, string $judul, string $pesan): void
    {
        http_response_code($kode);
        require __DIR__ . '/../../views/errors/error.php';
    }
}

if (!function_exists('panjang_teks')) {
    /** Jumlah karakter (UTF-8) tanpa bergantung pada ekstensi mbstring. */
    function panjang_teks(string $teks): int
    {
        return (int) preg_match_all('/./su', $teks);
    }
}
