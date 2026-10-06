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

5. **Barang** (`id_barang`, `kode_barang`, `nama_barang`, `harga_beli`, `harga_jual`, `stok`)

6. **Transaksi** (`id_transaksi`, `tanggal_transaksi`, `total_harga`, `uang_bayar`, `kembalian`)

7. **Detail Transaksi** (`id_detail`, `jumlah`, `harga_satuan`, `subtotal`)

*(Catatan: Diagram Entity-Relationship dan relasi antar tabel telah didokumentasikan di dalam BRD).*

## 🚀 Cara Menjalankan (Getting Started)

*(Bagian ini akan diperbarui setelah tahap penulisan kode dimulai)*

* Persyaratan Sistem: \[PHP 8, MySQL, Node.js\]

* Cara Install:

  1. Clone repository ini.

  2. ...

  3. ...

*Dibuat untuk keperluan tugas / proyek pengembangan sistem E-commerce POS UAD.*
