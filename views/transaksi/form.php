<?php
/** @var string $aksi */
/** @var int|null $id */
/** @var array $data */
/** @var array $galat */
/** @var \UMM\Pemweb\Model\Kategori[] $daftarKategori */
/** @var \UMM\Pemweb\Model\Dompet[] $daftarDompet */
require __DIR__ . '/../layout/header.php';
?>
<h1><?= e($judul) ?></h1>

<?php if ($daftarKategori === [] || $daftarDompet === []): ?>
    <p class="kosong">
        Sebelum mencatat transaksi, siapkan dulu
        <?= $daftarKategori === [] ? '<a href="' . e(url('/kategori/tambah')) . '">kategori</a>' : '' ?>
        <?= ($daftarKategori === [] && $daftarDompet === []) ? ' dan ' : '' ?>
        <?= $daftarDompet === [] ? '<a href="' . e(url('/dompet/tambah')) . '">dompet</a>' : '' ?>.
    </p>
<?php else: ?>
<form class="form" method="post" action="<?= e(url($aksi)) ?>" novalidate>
    <?= csrf_field() ?>
    <?php if ($id !== null): ?><input type="hidden" name="id" value="<?= e((string) $id) ?>"><?php endif; ?>

    <div class="field">
        <label for="kategori_id">Kategori</label>
        <select id="kategori_id" name="kategori_id" required<?= isset($galat['kategori_id']) ? ' aria-invalid="true" aria-describedby="err-kategori"' : '' ?>>
            <option value="">Pilih kategori</option>
            <?php foreach (['pemasukan' => 'Pemasukan', 'pengeluaran' => 'Pengeluaran'] as $tipe => $label): ?>
                <optgroup label="<?= e($label) ?>">
                    <?php foreach ($daftarKategori as $k): ?>
                        <?php if ($k->tipe === $tipe): ?>
                            <option value="<?= e((string) $k->id) ?>"<?= (string) $k->id === $data['kategori_id'] ? ' selected' : '' ?>><?= e($k->nama) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
        <?php if (isset($galat['kategori_id'])): ?><p class="galat" id="err-kategori"><?= e($galat['kategori_id']) ?></p><?php endif; ?>
    </div>

    <div class="field">
        <label for="dompet_id">Dompet</label>
        <select id="dompet_id" name="dompet_id" required<?= isset($galat['dompet_id']) ? ' aria-invalid="true" aria-describedby="err-dompet"' : '' ?>>
            <option value="">Pilih dompet</option>
            <?php foreach ($daftarDompet as $d): ?>
                <option value="<?= e((string) $d->id) ?>"<?= (string) $d->id === $data['dompet_id'] ? ' selected' : '' ?>><?= e($d->nama) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($galat['dompet_id'])): ?><p class="galat" id="err-dompet"><?= e($galat['dompet_id']) ?></p><?php endif; ?>
    </div>

    <div class="field">
        <label for="nominal">Nominal (Rp)</label>
        <input type="text" inputmode="decimal" id="nominal" name="nominal" value="<?= e($data['nominal']) ?>" required<?= isset($galat['nominal']) ? ' aria-invalid="true" aria-describedby="err-nominal"' : '' ?>>
        <?php if (isset($galat['nominal'])): ?><p class="galat" id="err-nominal"><?= e($galat['nominal']) ?></p><?php endif; ?>
    </div>

    <div class="field">
        <label for="tanggal">Tanggal</label>
        <input type="date" id="tanggal" name="tanggal" value="<?= e($data['tanggal']) ?>" required<?= isset($galat['tanggal']) ? ' aria-invalid="true" aria-describedby="err-tanggal"' : '' ?>>
        <?php if (isset($galat['tanggal'])): ?><p class="galat" id="err-tanggal"><?= e($galat['tanggal']) ?></p><?php endif; ?>
    </div>

    <div class="field">
        <label for="keterangan">Keterangan (opsional)</label>
        <input type="text" id="keterangan" name="keterangan" maxlength="255" value="<?= e($data['keterangan']) ?>"<?= isset($galat['keterangan']) ? ' aria-invalid="true" aria-describedby="err-ket"' : '' ?>>
        <?php if (isset($galat['keterangan'])): ?><p class="galat" id="err-ket"><?= e($galat['keterangan']) ?></p><?php endif; ?>
    </div>

    <div class="form-aksi">
        <button type="submit" class="btn">Simpan</button>
        <a href="<?= e(url('/transaksi')) ?>">Batal</a>
    </div>
</form>
<?php endif; ?>
<?php require __DIR__ . '/../layout/footer.php'; ?>
