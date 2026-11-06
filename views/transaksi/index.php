<?php
/** @var string|null $bulan */
/** @var array $daftar */
/** @var array $total */
require __DIR__ . '/../layout/header.php';
?>
<div class="judul-baris">
    <h1>Transaksi</h1>
    <a class="btn" href="<?= e(url('/transaksi/tambah')) ?>">Tambah transaksi</a>
</div>

<form class="filter" method="get" action="<?= e(url('/transaksi')) ?>">
    <label for="bulan">Bulan</label>
    <input type="month" id="bulan" name="bulan" value="<?= e((string) $bulan) ?>">
    <button type="submit" class="btn btn-sekunder">Terapkan</button>
    <?php if ($bulan !== null): ?><a href="<?= e(url('/transaksi')) ?>">Tampilkan semua</a><?php endif; ?>
</form>

<div class="kartu-grid">
    <div class="kartu">
        <span class="kartu-label">Pemasukan<?= $bulan !== null ? '' : ' (semua)' ?></span>
        <span class="kartu-nilai positif"><?= e(rupiah($total['pemasukan'])) ?></span>
    </div>
    <div class="kartu">
        <span class="kartu-label">Pengeluaran<?= $bulan !== null ? '' : ' (semua)' ?></span>
        <span class="kartu-nilai negatif"><?= e(rupiah($total['pengeluaran'])) ?></span>
    </div>
</div>

<?php if ($daftar === []): ?>
    <p class="kosong">
        <?= $bulan !== null ? 'Tidak ada transaksi pada bulan itu.' : 'Belum ada transaksi.' ?>
        <a href="<?= e(url('/transaksi/tambah')) ?>">Catat transaksi</a>
    </p>
<?php else: ?>
    <div class="tabel-bungkus">
        <table>
            <thead>
            <tr>
                <th scope="col">Tanggal</th><th scope="col">Kategori</th><th scope="col">Dompet</th>
                <th scope="col">Keterangan</th><th scope="col" class="angka">Nominal</th><th scope="col">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($daftar as $t): ?>
                <tr>
                    <td><?= e(tanggal_id($t['tanggal'])) ?></td>
                    <td><?= e($t['kategori']) ?></td>
                    <td><?= e($t['dompet']) ?></td>
                    <td><?= e($t['keterangan']) ?></td>
                    <td class="angka <?= $t['tipe'] === 'pemasukan' ? 'positif' : 'negatif' ?>">
                        <?= $t['tipe'] === 'pemasukan' ? '+' : '-' ?><?= e(rupiah($t['nominal'])) ?>
                    </td>
                    <td class="aksi">
                        <a href="<?= e(url('/transaksi/ubah?id=' . $t['id'])) ?>">Ubah</a>
                        <form method="post" action="<?= e(url('/transaksi/hapus')) ?>" onsubmit="return confirm('Hapus transaksi ini?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $t['id']) ?>">
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
