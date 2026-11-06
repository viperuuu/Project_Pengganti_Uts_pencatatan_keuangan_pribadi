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
        <label for="nama">Nama kategori</label>
        <input type="text" id="nama" name="nama" maxlength="50" value="<?= e($data['nama']) ?>" required<?= isset($galat['nama']) ? ' aria-invalid="true" aria-describedby="err-nama"' : '' ?>>
        <?php if (isset($galat['nama'])): ?><p class="galat" id="err-nama"><?= e($galat['nama']) ?></p><?php endif; ?>
    </div>

    <div class="field">
        <label for="tipe">Tipe</label>
        <select id="tipe" name="tipe" required<?= isset($galat['tipe']) ? ' aria-invalid="true" aria-describedby="err-tipe"' : '' ?>>
            <option value="pemasukan"<?= $data['tipe'] === 'pemasukan' ? ' selected' : '' ?>>Pemasukan</option>
            <option value="pengeluaran"<?= $data['tipe'] === 'pengeluaran' ? ' selected' : '' ?>>Pengeluaran</option>
        </select>
        <?php if (isset($galat['tipe'])): ?><p class="galat" id="err-tipe"><?= e($galat['tipe']) ?></p><?php endif; ?>
    </div>

    <div class="form-aksi">
        <button type="submit" class="btn">Simpan</button>
        <a href="<?= e(url('/kategori')) ?>">Batal</a>
    </div>
</form>
<?php require __DIR__ . '/../layout/footer.php'; ?>
