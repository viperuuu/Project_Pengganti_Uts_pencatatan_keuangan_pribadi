<?php
/** @var array $daftar */
require __DIR__ . '/../layout/header.php';
?>
<div class="judul-baris">
    <h1>Kategori</h1>
    <a class="btn" href="<?= e(url('/kategori/tambah')) ?>">Tambah kategori</a>
</div>

<?php if ($daftar === []): ?>
    <p class="kosong">Belum ada kategori. Tambahkan kategori seperti "Makan" atau "Uang saku" agar transaksi bisa dikelompokkan.</p>
<?php else: ?>
    <div class="tabel-bungkus">
        <table>
            <thead>
            <tr><th scope="col">Nama</th><th scope="col">Tipe</th><th scope="col" class="angka">Dipakai</th><th scope="col">Aksi</th></tr>
            </thead>
            <tbody>
            <?php foreach ($daftar as $k): ?>
                <tr>
                    <td><?= e($k['nama']) ?></td>
                    <td><span class="lencana <?= e($k['tipe']) ?>"><?= e(ucfirst($k['tipe'])) ?></span></td>
                    <td class="angka"><?= e((string) $k['jumlah_transaksi']) ?> transaksi</td>
                    <td class="aksi">
                        <a href="<?= e(url('/kategori/ubah?id=' . $k['id'])) ?>">Ubah</a>
                        <form method="post" action="<?= e(url('/kategori/hapus')) ?>" onsubmit="return confirm('Hapus kategori ini?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $k['id']) ?>">
                            <button type="submit" class="btn-teks bahaya">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../layout/footer.php'; ?>
