-- database/seed.sql — OPSIONAL: kategori dan dompet awal agar bisa langsung mencatat.
-- Jalankan SETELAH schema.sql. Aman dijalankan ulang (INSERT IGNORE).
USE keuangan;

INSERT IGNORE INTO kategori (nama, tipe) VALUES
    ('Uang saku', 'pemasukan'),
    ('Gaji', 'pemasukan'),
    ('Lainnya', 'pemasukan'),
    ('Makan', 'pengeluaran'),
    ('Transport', 'pengeluaran'),
    ('Pendidikan', 'pengeluaran'),
    ('Hiburan', 'pengeluaran'),
    ('Tagihan', 'pengeluaran');

INSERT IGNORE INTO dompet (nama, saldo_awal) VALUES
    ('Tunai', 0),
    ('Rekening bank', 0);
