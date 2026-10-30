# warisanku_sambas
warisan sambas sampai ke kita untuk di lanjutkan dan dibudayakan

## Menjalankan panel admin (XAMPP)
1. Taruh folder ini di `htdocs/warisanku_sambas`, nyalakan Apache dan MySQL.
2. Impor `database/schema.sql` lewat phpMyAdmin (tab Import). Database, 6 tabel, dan kategori awal dibuat otomatis.
   Jika database sudah ada (mis. dari ekspor phpMyAdmin) tetapi kategorinya kosong, jalankan saja `database/seed_kategori.sql`.
3. Sesuaikan `config/database.php` dan `BASE_URL` di `config/app.php` bila perlu.
4. Buka `http://localhost/warisanku_sambas/admin/setup.php`, buat akun admin pertama, lalu HAPUS file `admin/setup.php`.
5. Login di `http://localhost/warisanku_sambas/admin/login.php`.

Butuh PHP 8.1+ dengan ekstensi `pdo_mysql`, `fileinfo`, dan `mbstring`.

## Menu admin
Sejarah, Budaya, Wisata, Galeri, Kategori (semua peran) dan Pengguna (khusus admin).
Gaya panel admin ada di `public/assets/css/admin.css` dan tidak perlu build Tailwind.

## Halaman publik
Halaman publik (`public/index.php`, `pages/*.php`) membaca konten dari database lewat `includes/konten.php`.
Hanya konten berstatus **Terbit** yang tampil; yang berstatus Draf hanya terlihat di panel admin.
Foto yang diunggah lewat admin tersimpan di `public/uploads/`. `includes/data-contoh.php` sudah tidak dipakai dan boleh dihapus.
