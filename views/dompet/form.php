<?php
/** @var string $aksi */
/** @var int|null $id */
/** @var array $data */
/** @var array $galat */
require __DIR__ . '/../layout/header.php';
?>
<h1><?= e($judul) ?></h1>

<form class="form" method="post" action="<?= e(url($aksi)) ?>" novalidate>
    <?= csrf_field() ?>
    <?php if ($id !== null): ?><input type="hidden" name="id" value="<?= e((string) $id) ?>"><?php endif; ?>

    <div class="field">
        <label for="nama">Nama dompet</label>
        <input type="text" id="nama" name="nama" maxlength="50" value="<?= e($data['nama']) ?>" required<?= isset($galat['nama']) ? ' aria-invalid="true" aria-describedby="err-nama"' : '' ?>>
        <?php if (isset($galat['nama'])): ?><p class="galat" id="err-nama"><?= e($galat['nama']) ?></p><?php endif; ?>
    </div>

    <div class="field">
        <label for="saldo_awal">Saldo awal (Rp)</label>
        <input type="text" inputmode="decimal" id="saldo_awal" name="saldo_awal" value="<?= e($data['saldo_awal']) ?>" required<?= isset($galat['saldo_awal']) ? ' aria-invalid="true" aria-describedby="err-saldo"' : '' ?>>
        <?php if (isset($galat['saldo_awal'])): ?><p class="galat" id="err-saldo"><?= e($galat['saldo_awal']) ?></p><?php endif; ?>
    </div>

    <div class="form-aksi">
        <button type="submit" class="btn">Simpan</button>
        <a href="<?= e(url('/dompet')) ?>">Batal</a>
    </div>
</form>
<?php require __DIR__ . '/../layout/footer.php'; ?>
