# TokoKu POS (Laravel 11/12) — cara pasang

Folder ini adalah "overlay": ditimpa ke proyek Laravel baru (PHP 8.2+).

## 1. Buat proyek (di Terminal Laragon)
    cd C:\laragon\www
    composer create-project laravel/laravel tokoku
    cd tokoku

## 2. Salin isi zip ke proyek
Salin folder `app`, `config`, `database`, `public`, `resources`, `routes` ke `C:\laragon\www\tokoku`, pilih Replace/Timpa.
File bawaan Laravel yang ikut tertimpa: `app/Models/User.php`, `app/Providers/AppServiceProvider.php`,
`routes/web.php`, `database/seeders/DatabaseSeeder.php`.

## 3. Atur .env
    APP_NAME=TokoKu
    APP_DEBUG=true            # tombol "Akun Pengujian Demo" di halaman login hanya muncul saat true
    APP_TIMEZONE=Asia/Jakarta # (kalau baris ini tidak ada, ubah 'timezone' di config/app.php jadi 'Asia/Jakarta')
    # Opsional, tampil di struk:
    STORE_NAME="TOKOKU POS"
    STORE_ADDRESS="Alamat toko kamu"
    STORE_PHONE="(021) 789-1234"

## 4. Jalankan
    php artisan migrate --seed
    php artisan storage:link      # WAJIB agar foto yang diupload tampil
    php artisan serve

Buka http://localhost:8000

## Akun contoh (password semuanya: 123)
| Username | Peran | Status |
|---|---|---|
| admin | Admin (Pemilik) | aktif |
| budi | Kasir | aktif |
| siti | Kasir | aktif |
| ahmad | Kasir | nonaktif (untuk tes tolak login) |
Ganti password sebelum dipakai sungguhan (menu Kelola Akun Kasir).

## Fitur (mengikuti aplikasi React TokoKu)
- Login pakai USERNAME, akun nonaktif ditolak
- Transaksi Kasir: katalog berfoto, cari nama/barcode/kategori, input barcode + simulasi scan, filter kategori,
  keranjang, metode bayar Tunai / QRIS / Debit-Transfer, uang diterima + tombol cepat, kembalian, catatan,
  stok berkurang otomatis, nomor invoice TRX-YYYYMMDD-0001
- Struk thermal (modal): Salin Struk, Cetak, lebar 58mm/80mm (config/tokoku.php -> paper_width)
- Kelola Produk & Foto (admin): barcode, satuan, deskripsi, UPLOAD FOTO (JPG/PNG/WEBP maks 2 MB) atau URL gambar,
  Tambah Stok, filter stok menipis/habis. Kasir hanya melihat "Katalog & Cek Stok".
- Riwayat Transaksi: admin semua, kasir hanya miliknya; rincian barang per transaksi
- Laporan Penjualan (admin): Hari Ini/Kemarin/7 Hari/Bulan Ini/Semua, filter kasir, omzet, produk terlaris,
  metode pembayaran, unduh CSV, cetak
- Kelola Akun Kasir (admin): peran, status aktif/nonaktif, HP, email

## Catatan
- Foto contoh produk memakai URL Unsplash, jadi butuh internet. Foto yang kamu upload tersimpan di storage/app/public/produk.
- Tab "Backend Laravel 11" di aplikasi React tidak dibawa, karena ini sudah backend Laravel sungguhan.
- Hosting (Hostinger): arahkan document root ke folder `public`; jika symlink storage tidak bisa, salin isi
  `storage/app/public` ke `public/storage`.
