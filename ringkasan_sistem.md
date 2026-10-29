# Ringkasan Sistem Kasir (POS) Gorengan Mama Ilham

Berikut adalah rangkuman lengkap mengenai arsitektur, database, dan fitur dari sistem aplikasi **Gorengan Mama Ilham** untuk disalin ke Gemini guna penulisan skripsi (seperti Bab III Analisis & Perancangan atau Bab IV Implementasi).

---

## 1. Deskripsi Umum Sistem
Aplikasi **Gorengan Mama Ilham** adalah sistem kasir atau *Point of Sale* (POS) berbasis web yang dirancang khusus untuk mempermudah pencatatan transaksi, manajemen stok menu, pengelolaan pengguna (staf kasir), serta pembuatan laporan penjualan. Aplikasi ini memiliki dua peran utama (role): **Admin** (mengelola menu, pengguna, dan laporan penjualan) serta **Kasir** (melakukan pencatatan transaksi penjualan).

---

## 2. Tech Stack (Spesifikasi Teknologi)
*   **Back-End Framework**: Laravel 11 (PHP 8.2+) dengan arsitektur MVC (Model-View-Controller).
*   **Front-End**: HTML5, Tailwind CSS (via CDN untuk penyusunan antarmuka responsif dan modern), serta Alpine.js (untuk reaktivitas sisi klien seperti keranjang belanja kasir dan pencarian).
*   **Database**: MySQL / MariaDB dengan pengelolaan migrasi menggunakan Laravel Migrations.
*   **Payment Gateway**: Midtrans (simulasi integrasi metode pembayaran non-tunai melalui QRIS menggunakan *Midtrans Snap API*).
*   **Ekspor Data**: Fitur ekspor laporan penjualan ke format CSV dan integrasi alur kerja impor otomatis ke Google Sheets.

---

## 3. Struktur Database & Skema Relasi (ERD)
Sistem ini menggunakan 4 tabel utama yang terhubung secara relasional:

### a. Tabel `users` (Manajemen Pengguna)
Menyimpan informasi pengguna yang berhak mengakses sistem kasir.
*   `id` (BigInt, PK, Auto Increment)
*   `name` (Varchar): Nama lengkap pengguna.
*   `email` (Varchar, Unique): Email untuk proses masuk (login).
*   `password` (Varchar): Kata sandi terenkripsi (bcrypt/hashed).
*   `role` (Enum): Hak akses pengguna, bernilai `'admin'` atau `'kasir'`.
*   `timestamps` (`created_at`, `updated_at`)

### b. Tabel `menus` (Daftar Menu Makanan)
Menyimpan data barang/makanan yang dijual.
*   `id` (BigInt, PK, Auto Increment)
*   `name` (Varchar): Nama menu makanan (misal: Bakwan, Tempe Mendoan).
*   `price` (Integer): Harga satuan menu.
*   `image` (Varchar): Path file gambar menu yang disimpan pada penyimpanan publik (`storage/app/public/menus`).
*   `is_active` (Boolean): Status aktif menu. Jika `false`, menu tidak muncul di halaman kasir.
*   `timestamps` (`created_at`, `updated_at`)

### c. Tabel `transactions` (Header Transaksi)
Menyimpan informasi utama dari setiap nota/struk belanja yang berhasil diproses.
*   `id` (BigInt, PK, Auto Increment)
*   `receipt_number` (Varchar, Unique): Nomor nota transaksi (format: `#0001`, `#0002`, dst).
*   `user_id` (BigInt, FK ke `users.id`): Kasir yang melayani transaksi.
*   `total` (Integer): Total nominal belanja yang harus dibayar.
*   `payment_method` (Enum): Metode pembayaran yang digunakan, bernilai `'tunai'` atau `'qris'`.
*   `cash_received` (Integer, Nullable): Nominal uang tunai yang diterima dari pembeli (hanya untuk metode tunai).
*   `change_amount` (Integer, Nullable): Jumlah uang kembalian (hanya untuk metode tunai).
*   `timestamps` (`created_at`, `updated_at`)

### d. Tabel `transaction_items` (Detail Item Transaksi)
Menyimpan rincian menu apa saja yang dibeli pada setiap transaksi.
*   `id` (BigInt, PK, Auto Increment)
*   `transaction_id` (BigInt, FK ke `transactions.id`, Cascade on Delete)
*   `menu_id` (BigInt, FK ke `menus.id`): Menu yang dibeli.
*   `qty` (Integer): Jumlah porsi yang dibeli.
*   `price` (Integer): Harga satuan menu saat transaksi terjadi (untuk mengantisipasi perubahan harga menu di masa depan).
*   `subtotal` (Integer): Total harga per baris item (`qty` dikali `price`).
*   `timestamps` (`created_at`, `updated_at`)

---

## 4. Fitur Utama & Alur Kerja Sistem

### A. Autentikasi dan Pembatasan Hak Akses (Middleware)
*   Menggunakan sistem autentikasi bawaan Laravel (`Auth::attempt`).
*   **AdminMiddleware**: Membatasi rute-rute khusus administrator (seperti kelola menu, kelola user, dan laporan). Jika user non-admin mencoba mengakses, sistem mengembalikan respon HTTP 403 (Forbidden).

### B. Dashboard Analitik
Menyajikan statistik ringkas performa bisnis secara real-time untuk Admin:
*   **Total Pendapatan**: Akumulasi pendapatan kotor dari awal transaksi.
*   **Total Transaksi**: Jumlah pesanan yang tercatat dalam database.
*   **Rata-rata Transaksi**: Rata-rata nominal belanja per struk.
*   **Menu Terlaris**: Menu dengan akumulasi kuantitas (`qty`) penjualan terbanyak.
*   **Grafik Popularitas Menu**: Top 5 menu terlaris yang divisualisasikan dengan persentase bar dinamis (Tailwind + Blade).
*   **Daftar Transaksi Terbaru**: Tabel ringkas transaksi terakhir dengan pagination.

### C. Sistem Kasir (Point of Sale) Interaktif
*   Kasir dapat memilih item menu dengan menekan kartu menu, memasukkan jumlah kuantitas, dan melihat perubahan total belanja secara instan di panel kanan (ditenagai oleh JavaScript/Alpine.js).
*   **Pembayaran Tunai**: Mengharuskan penginputan uang diterima (`cash_received`). Sistem memvalidasi agar uang tidak kurang dari total belanja, lalu otomatis menampilkan uang kembalian (`change_amount`).
*   **Pembayaran QRIS**:
    *   Sistem mengirim data keranjang ke **MidtransController**.
    *   Menggunakan **Midtrans Shim** (`App\MidtransShim`), yaitu kelas kustom ringan buatan sendiri untuk berkomunikasi langsung dengan API Midtrans Snap menggunakan HTTP Client bawaan Laravel (tanpa pustaka vendor pihak ketiga yang berat).
    *   Midtrans memproses data transaksi dan mengembalikan **Snap Token** beserta URL pembayaran.
    *   Halaman kasir memunculkan *pop-up* pembayaran Midtrans Snap secara langsung di atas halaman kasir melalui script `snap.js`.

### D. Keamanan Data Transaksi (ACID Compliance)
*   Penyimpanan transaksi menggunakan **Database Transaction** (`DB::transaction`).
*   Jika penulisan data ke tabel `transactions` berhasil tetapi penulisan item ke `transaction_items` gagal, seluruh operasi akan dibatalkan (*rollback*). Hal ini mencegah data yatim piatu (*orphan data*) dan menjamin integritas data keuangan.

### E. Manajemen Data Menu & Pengguna (Admin Panel)
*   **CRUD Menu**: Admin dapat menambahkan menu baru dengan mengunggah gambar. Sistem secara otomatis menyimpan file ke penyimpanan lokal (`storage`) dan menghapus file lama jika gambar diperbarui.
*   *Soft Restriction*: Jika admin ingin menghapus menu yang sudah pernah digunakan dalam transaksi sebelumnya, sistem mencegah penghapusan fisik dari database agar tidak merusak laporan riwayat transaksi. Sebagai gantinya, sistem mengubah status menu menjadi tidak aktif (`is_active = false`).
*   **CRUD Pengguna**: Admin dapat menambahkan akun kasir baru, memperbarui kata sandi kasir, dan menghapus akun staf (dengan validasi mencegah admin menghapus akunnya sendiri).

### F. Filter Penjualan & Ekspor Laporan
*   Laporan penjualan dapat difilter berdasarkan rentang tanggal (`date_from` dan `date_to`).
*   **Ekspor CSV**: Mengonversi data transaksi, popularitas menu, dan rincian item dalam rentang tanggal tersebut menjadi file `.csv` dengan pengodean UTF-8 (agar format karakter aman).
*   **Ekspor Google Sheets**: Fitur unik yang mengunduh file CSV secara otomatis di latar belakang, lalu membuka tab browser baru menuju Google Sheets (`https://sheets.google.com/create`). Admin dipandu dengan langkah-langkah mudah untuk mengimpor file CSV ke Google Sheets tanpa memerlukan integrasi API OAuth Google yang rumit.

---

## 5. Struktur Berkas (File Structure) Penting
Berikut adalah daftar file utama yang mengontrol logika sistem:
*   `routes/web.php`: Rute URL aplikasi dan penempatan middleware keamanan.
*   `app/Http/Controllers/`:
    *   `AuthController.php`: Mengatur proses masuk dan keluar aplikasi.
    *   `DashboardController.php`: Menghitung data agregat statistik dashboard.
    *   `KasirController.php`: Menampilkan menu aktif pada antarmuka kasir.
    *   `TransactionController.php`: Menyimpan data transaksi ke DB dengan `DB::transaction`.
    *   `MidtransController.php`: Menghubungkan kasir dengan payment gateway Midtrans.
    *   `ReportController.php`: Menangani permintaan laporan penjualan, ekspor CSV, dan Google Sheets.
    *   `Admin/UserController.php` & `Admin/MenuController.php`: Mengontrol CRUD data dasar.
*   `app/Models/`: `User.php`, `Menu.php`, `Transaction.php`, `TransactionItem.php` (menentukan relasi database).
*   `app/Services/ReportService.php`: Logika bisnis pemrosesan data laporan penjualan dan penyusunan string CSV.
*   `app/MidtransShim.php`: Kelas bantuan buatan sendiri untuk memicu request ke Midtrans Snap API menggunakan HTTP Client Laravel.
*   `resources/views/layouts/app.blade.php`: Layout dasar aplikasi yang memuat Tailwind CSS, Alpine.js, dan Midtrans Snap JS.
