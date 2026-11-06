<?php
/** @var int $kode */
/** @var string $judul */
/** @var string $pesan */
require __DIR__ . '/../layout/header.php';
?>
<h1><?= e((string) $kode) ?> — <?= e($judul) ?></h1>
<p class="lead"><?= e($pesan) ?></p>
<p><a href="<?= e(url('/')) ?>">Kembali ke beranda</a></p>
<?php require __DIR__ . '/../layout/footer.php'; ?>
