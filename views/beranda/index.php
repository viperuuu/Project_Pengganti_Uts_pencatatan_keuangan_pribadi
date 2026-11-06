<?php
/** @var array|null $ringkasan */
/** @var string|null $galat */
require __DIR__ . '/../layout/header.php';
?>
<h1>Catatan Keuangan</h1>

<?php if ($galat !== null): ?>
    <section class="panel" aria-labelledby="judul-status">
        <h2 id="judul-status">Database belum siap</h2>
        <p class="catatan"><?= e($galat) ?></p>
        <p class="catatan">Pastikan MySQL menyala, database <code>keuangan</code> sudah diimpor dari <code>database/schema.sql</code>, dan kredensial di <code>src/config/koneksi.php</code> sudah benar.</p>
    </section>
<?php else: ?>
    <p class="lead">Ringkasan keuanganmu untuk bulan <?= e(date('m/Y', strtotime($ringkasan['bulan'] . '-01'))) ?>.</p>

    <div class="kartu-grid">
        <div class="kartu">
            <span class="kartu-label">Total saldo</span>
            <span class="kartu-nilai"><?= e(rupiah($ringkasan['saldo_total'])) ?></span>
        </div>
        <div class="kartu">
            <span class="kartu-label">Pemasukan bulan ini</span>
            <span class="kartu-nilai positif"><?= e(rupiah($ringkasan['pemasukan'])) ?></span>
        </div>
        <div class="kartu">
            <span class="kartu-label">Pengeluaran bulan ini</span>
            <span class="kartu-nilai negatif"><?= e(rupiah($ringkasan['pengeluaran'])) ?></span>
        </div>
    </div>

    <h2>Saldo per dompet</h2>
    <?php if ($ringkasan['dompet'] === []): ?>
        <p class="kosong">Belum ada dompet. <a href="<?= e(url('/dompet/tambah')) ?>">Tambah dompet pertama</a> untuk mulai mencatat.</p>
    <?php else: ?>
        <div class="tabel-bungkus">
            <table>
                <thead><tr><th scope="col">Dompet</th><th scope="col" class="angka">Saldo</th></tr></thead>
                <tbody>
                <?php foreach ($ringkasan['dompet'] as $d): ?>
                    <tr>
                        <td><?= e($d['nama']) ?></td>
                        <td class="angka"><?= e(rupiah($d['saldo'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="judul-baris">
        <h2>Transaksi terbaru</h2>
        <a class="btn" href="<?= e(url('/transaksi/tambah')) ?>">Tambah transaksi</a>
    </div>
    <?php if ($ringkasan['terbaru'] === []): ?>
        <p class="kosong">Belum ada transaksi. Catat pemasukan atau pengeluaran pertamamu.</p>
    <?php else: ?>
        <div class="tabel-bungkus">
            <table>
                <thead>
                <tr><th scope="col">Tanggal</th><th scope="col">Kategori</th><th scope="col">Dompet</th><th scope="col" class="angka">Nominal</th></tr>
                </thead>
                <tbody>
                <?php foreach ($ringkasan['terbaru'] as $t): ?>
                    <tr>
                        <td><?= e(tanggal_id($t['tanggal'])) ?></td>
                        <td><?= e($t['kategori']) ?></td>
                        <td><?= e($t['dompet']) ?></td>
                        <td class="angka <?= $t['tipe'] === 'pemasukan' ? 'positif' : 'negatif' ?>">
                            <?= $t['tipe'] === 'pemasukan' ? '+' : '-' ?><?= e(rupiah($t['nominal'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/../layout/footer.php'; ?>
