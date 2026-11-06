<?php
// src/config/koneksi.php — mengembalikan objek PDO.
// Sesuaikan nilai di bawah dengan MySQL di komputer masing-masing (XAMPP: user root, password kosong).

return new PDO(
    'mysql:host=localhost;dbname=keuangan;charset=utf8mb4',
    'root',
    '',
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
);
