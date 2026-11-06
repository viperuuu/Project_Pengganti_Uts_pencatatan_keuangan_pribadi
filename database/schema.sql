-- database/schema.sql — impor lewat phpMyAdmin (tab Import) atau: mysql -u root < database/schema.sql

CREATE DATABASE IF NOT EXISTS keuangan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE keuangan;

CREATE TABLE IF NOT EXISTS kategori (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(50) NOT NULL,
    tipe ENUM('pemasukan', 'pengeluaran') NOT NULL,
    UNIQUE KEY uq_kategori_nama_tipe (nama, tipe)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS dompet (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama       VARCHAR(50) NOT NULL UNIQUE,
    saldo_awal DECIMAL(15,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS transaksi (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT UNSIGNED NOT NULL,
    dompet_id   INT UNSIGNED NOT NULL,
    nominal     DECIMAL(15,2) NOT NULL CHECK (nominal > 0),
    tanggal     DATE NOT NULL,
    keterangan  VARCHAR(255) NULL,
    dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_transaksi_kategori FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE RESTRICT,
    CONSTRAINT fk_transaksi_dompet   FOREIGN KEY (dompet_id)   REFERENCES dompet(id)   ON DELETE RESTRICT,
    INDEX idx_transaksi_tanggal (tanggal)
) ENGINE=InnoDB;
