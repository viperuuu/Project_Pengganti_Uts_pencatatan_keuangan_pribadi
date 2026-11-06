<?php
/** @var string $judul */
$menu = [
    '/'          => 'Beranda',
    // '/transaksi' => 'Transaksi',
    '/kategori'  => 'Kategori',
    '/dompet'    => 'Dompet',
];
$pathSaatIni = '/' . trim(substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/', strlen(BASE_URL)), '/');
$aktif = static fn (string $path): bool => $path === '/' ? $pathSaatIni === '/' : str_starts_with($pathSaatIni, $path);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul ?? 'Keuangan') ?> | Catatan Keuangan</title>
    <link rel="stylesheet" href="<?= e(url('/css/style.css')) ?>">
</head>
<body>
<header class="topbar">
    <div class="wrap topbar-isi">
        <a class="brand" href="<?= e(url('/')) ?>">Catatan Keuangan</a>
        <nav aria-label="Menu utama">
            <?php foreach ($menu as $path => $label): ?>
                <a href="<?= e(url($path)) ?>"<?= $aktif($path) ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>
<main class="wrap">
<?php foreach (\UMM\Pemweb\Core\Flash::ambil() as $f): ?>
    <div class="flash <?= e($f['tipe']) ?>" role="status"><?= e($f['pesan']) ?></div>
<?php endforeach; ?>
