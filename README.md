# Point of Sale (POS) System 🛒
# Keluarga-Tanpa-KK

> **Status:** 🚧 Work in Progress (Sedang dalam masa pengembangan)

Sebuah platform sistem Point Of Sale (POS) yang dirancang khusus untuk mempermudah pencatatan transaksi penjualan, pengelolaan stok barang, serta pemantauan laporan penjualan dan kondisi keuangan sebuah toko.

Proyek ini dikembangkan oleh **Kelompok KTKK**.

## 👥 Pemangku Kepentingan (Stakeholders)

Sistem ini dirancang untuk mengakomodasi beberapa peran pengguna:

* **Pemilik Usaha (Owner):** Memantau penjualan, stok barang, melihat barang paling laris/jarang terjual, serta memantau laba rugi.

* **Kasir / Operator:** Melakukan pencatatan transaksi secara cepat, akurat, dan otomatis menghitung total bayar serta kembalian.

* **Admin:** Mengelola data *master* (data barang, harga, dan stok) agar selalu valid dan mutakhir.

* **Pelanggan:** Mendapatkan pelayanan transaksi yang cepat dengan perhitungan dan informasi harga yang sesuai.

## ✨ Fitur Utama

Berdasarkan *Business Requirement Document* (BRD), sistem ini akan memiliki fungsionalitas berikut:

### 🛍️ Modul Transaksi & Kasir

* Pencatatan transaksi penjualan secara *real-time*.

* Pencarian dan pemilihan barang menggunakan kode barang unik.

* Perhitungan total harga otomatis.

* Perhitungan uang kembalian pelanggan.

### 📦 Modul Manajemen Barang & Stok

* Penambahan, pembaruan, dan pengelolaan data barang (Kode, Nama, Harga Beli, Harga Jual).

* Pembaruan stok barang secara otomatis setiap ada transaksi berhasil.

* Menampilkan informasi jumlah ketersediaan stok barang.

### 📊 Modul Laporan & Analitik

* Menampilkan riwayat dan ringkasan transaksi penjualan.

* Mengidentifikasi barang paling laris dan barang jarang dibeli.

* Laporan laba/rugi berdasarkan data penjualan dibandingkan dengan modal (harga beli).

## 🗄️ Skema Database

Sistem ini menggunakan Relational Database dengan entitas utama sebagai berikut:

1. **Admin** (`id_admin`, `nama`, `username`, `password`, `no_telp`)

2. **Owner** (`id_owner`, `nama`, `username`, `password`, `no_telp`)

3. **Kasir** (`id_kasir`, `nama`, `username`, `password`, `no_telp`)

4. **Pelanggan** (`id_pelanggan`, `nama`, `no_telp`)

5. **Barang** (`id_barang`, `id_admin`, `kode_barang`, `nama_barang`, `harga_beli`, `harga_jual`, `stok`)

6. **Transaksi** (`id_transaksi`, `kode_transaksi`, `tanggal_transaksi`, `id_kasir`, `id_pelanggan`, `total_harga`, `uang_bayar`, `kembalian`)

7. **Detail Transaksi** (`id_detail`, `jumlah`, `harga_satuan`, `harga_beli_satuan`, `subtotal`)

*(Catatan: Diagram Entity-Relationship dan relasi antar tabel telah didokumentasikan di dalam BRD).*

## 🚀 Cara Menjalankan (Getting Started)

Persyaratan: PHP 8.2+, Composer, dan SQLite atau MySQL.

1. Pasang dependency dengan `composer install`.
2. Salin `.env.example` menjadi `.env`, atur koneksi database, lalu jalankan `php artisan key:generate`.
3. Jalankan migrasi dan data awal dengan `php artisan migrate --seed`.
4. Jalankan aplikasi dengan `php artisan serve`, lalu buka alamat lokal yang ditampilkan.

Seeder menyiapkan 1 admin, 1 owner, 3 kasir, 20 barang, sedikitnya 100.000 pelanggan, dan 100.000 transaksi demo beserta detailnya. Setiap transaksi demo terhubung ke pelanggan, kasir, serta satu atau lebih barang; nilai total, pembayaran, kembalian, dan snapshot harga modal detail dihitung konsisten. Nomor transaksi menggunakan rentang `DEMO-*`. Seeder dapat dijalankan ulang tanpa menggandakan data demo atau mereset stok yang sudah berubah.

| Peran | Username | Password awal |
|---|---|---|
| Admin | `admin` | `password` |
| Kasir | `kasir` | `password` |
| Kasir | `kasir2` | `password` |
| Kasir | `kasir3` | `password` |
| Owner | `owner` | `password` |

Ganti nilai password default pada seeder sebelum aplikasi digunakan dengan data nyata, lalu jalankan ulang seeder untuk menerapkannya.

### Alur POS

- Admin dapat mencari, menambah, mengubah, serta menghapus barang dan pelanggan (data yang sudah terhubung ke transaksi tidak dapat dihapus). Kasir dapat mencari pelanggan saat transaksi.
- Admin dan kasir dapat membuat transaksi. Server memvalidasi pembayaran dan stok, menyimpan detail serta harga modal saat transaksi, lalu mengurangi stok dalam satu transaksi database.
- Semua peran dapat melihat daftar dan detail transaksi. Dashboard menampilkan ringkasan penjualan, estimasi laba kotor, barang terlaris, dan transaksi terbaru.
- Jalankan test terarah dengan `php artisan test`.

*Dibuat untuk keperluan tugas / proyek pengembangan sistem POS UAD.*
