<?php
/** @var array $daftar */
require __DIR__ . '/../layout/header.php';
?>
<div class="judul-baris">
    <h1>Dompet</h1>
    <a class="btn" href="<?= e(url('/dompet/tambah')) ?>">Tambah dompet</a>
</div>

<?php if ($daftar === []): ?>
    <p class="kosong">Belum ada dompet. Tambahkan sumber dana seperti "Tunai", "Rekening bank", atau "E-wallet".</p>
<?php else: ?>
    <div class="tabel-bungkus">
        <table>
            <thead>
            <tr>
                <th scope="col">Nama</th><th scope="col" class="angka">Saldo awal</th>
                <th scope="col" class="angka">Saldo sekarang</th><th scope="col" class="angka">Transaksi</th><th scope="col">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($daftar as $d): ?>
                <tr>
                    <td><?= e($d['nama']) ?></td>
                    <td class="angka"><?= e(rupiah($d['saldo_awal'])) ?></td>
                    <td class="angka"><?= e(rupiah($d['saldo'])) ?></td>
                    <td class="angka"><?= e((string) $d['jumlah_transaksi']) ?></td>
                    <td class="aksi">
                        <a href="<?= e(url('/dompet/ubah?id=' . $d['id'])) ?>">Ubah</a>
                        <form method="post" action="<?= e(url('/dompet/hapus')) ?>" onsubmit="return confirm('Hapus dompet ini?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $d['id']) ?>">
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
