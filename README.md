# Catatan Keuangan (PHP MVC + MySQL)

Aplikasi pencatat keuangan pribadi untuk tugas Pemrograman Web. Arsitektur:
Front Controller → Router → Controller → Repository (Model) → View.

## Fitur

- CRUD **kategori** (pemasukan/pengeluaran), **dompet** (dengan saldo awal), dan **transaksi**.
- Beranda berisi total saldo, pemasukan/pengeluaran bulan berjalan, saldo per dompet, dan 5 transaksi terbaru.
- Filter transaksi per bulan.
- Validasi di controller (status 422 + pesan per kolom), token CSRF di semua form POST, escape output dengan `e()`, prepared statement PDO, pola PRG setelah POST.
- Penghapusan kategori/dompet yang masih dipakai transaksi ditolak dengan pesan yang jelas.

## Skema database (3 tabel)

- `kategori(id, nama, tipe)`
- `dompet(id, nama, saldo_awal)`
- `transaksi(id, kategori_id → kategori, dompet_id → dompet, nominal, tanggal, keterangan, dibuat_pada)`

Saldo dompet tidak disimpan, tetapi dihitung: `saldo_awal + pemasukan − pengeluaran`.

## Tabel rute

| Method | Path | Aksi |
|---|---|---|
| GET | `/` | Beranda (rekap) |
| GET | `/kategori`, `/kategori/tambah`, `/kategori/ubah?id=` | Daftar, form tambah, form ubah |
| POST | `/kategori`, `/kategori/ubah`, `/kategori/hapus` | Simpan, perbarui, hapus |
| GET/POST | `/dompet...` | Sama seperti kategori |
| GET/POST | `/transaksi...` | Sama seperti kategori (+ `?bulan=YYYY-MM` untuk filter) |

## Struktur folder

```
keuangan-mvc/
├─ composer.json
├─ database/ (schema.sql, seed.sql)
├─ public/ (index.php, .htaccess, css/style.css)   ← satu-satunya folder web
├─ src/
│  ├─ Core/ (Router, Controller, Database, Flash, helpers)
│  ├─ Controllers/ (Beranda, Kategori, Dompet, Transaksi)
│  ├─ Model/ (Kategori, Dompet, Transaksi)
│  ├─ Repository/ (akses database, prepared statement)
│  ├─ Service/ (LaporanService)
│  └─ config/koneksi.php
└─ views/ (layout, beranda, kategori, dompet, transaksi, errors)
```

## Cara menjalankan

1. Nyalakan MySQL (XAMPP: Start Apache + MySQL).
2. Impor `database/schema.sql` lewat phpMyAdmin (tab Import). Opsional: impor juga `database/seed.sql` untuk kategori dan dompet awal.
3. Sesuaikan user/password di `src/config/koneksi.php` bila perlu.
4. Dari folder proyek jalankan: `php -S localhost:8000 -t public`, lalu buka http://localhost:8000
   (Alternatif XAMPP: taruh folder di `htdocs`, buka http://localhost/keuangan-mvc/public)

Composer tidak wajib; `public/index.php` punya autoloader cadangan.
Sebelum dikumpulkan, ubah `APP_DEBUG` di `public/index.php` menjadi `false`.

## Alur Git

- Jangan push langsung ke `main`; satu branch per fitur (`feature/kategori`, dst.), lalu Pull Request.
- Pull Request direview minimal satu rekan sebelum di-merge.
