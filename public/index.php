<?php
// public/index.php — FRONT CONTROLLER: satu pintu untuk semua request.

declare(strict_types=1);

use UMM\Pemweb\Controllers\DompetController;
use UMM\Pemweb\Core\Router;

// 1) Autoload: pakai Composer kalau ada, kalau tidak pakai autoloader sederhana.
$autoload = __DIR__ . '/../vendor/autoload.php';
if (is_file($autoload)) {
    require $autoload;
} else {
    spl_autoload_register(function (string $kelas): void {
        $prefix = 'UMM\\Pemweb\\';
        if (!str_starts_with($kelas, $prefix)) {
            return;
        }
        $path = __DIR__ . '/../src/' . str_replace('\\', '/', substr($kelas, strlen($prefix))) . '.php';
        if (is_file($path)) {
            require $path;
        }
    });
}

require __DIR__ . '/../src/Core/helpers.php';

// 2) Pengaturan dasar. Ubah APP_DEBUG ke false saat dikumpulkan/dipublikasikan.
const APP_DEBUG = true;
date_default_timezone_set('Asia/Jakarta');
session_start();

$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('BASE_URL', $basePath);

set_exception_handler(function (Throwable $e): void {
    error_log((string) $e);
    tampilkan_error(
        500,
        'Terjadi kesalahan',
        APP_DEBUG ? $e->getMessage() : 'Maaf, terjadi kesalahan pada server. Coba lagi beberapa saat.'
    );
});

// 3) Tabel rute: satu baris per rute.
$router = new Router();

// === RUTE BERANDA ===

// === RUTE KATEGORI ===

// === RUTE DOMPET ===
$router->get('/dompet', [DompetController::class, 'index']);
$router->get('/dompet/tambah', [DompetController::class, 'tambah']);
$router->post('/dompet', [DompetController::class, 'simpan']);
$router->get('/dompet/ubah', [DompetController::class, 'ubah']);
$router->post('/dompet/ubah', [DompetController::class, 'perbarui']);
$router->post('/dompet/hapus', [DompetController::class, 'hapus']);

// === RUTE TRANSAKSI ===

// 4) Path bersih (tanpa base path & query string), lalu dispatch.
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
if ($basePath !== '' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}
$uri = '/' . trim($uri, '/');

$router->dispatch($_SERVER['REQUEST_METHOD'], $uri);
