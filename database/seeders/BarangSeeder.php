<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::where('username', 'admin')->firstOrFail();

        foreach ([
            ['SKU-001', 'Air Mineral 600ml', 2000, 3500, 100],
            ['SKU-002', 'Roti Tawar', 12000, 16000, 40],
            ['SKU-003', 'Mi Instan', 2500, 3500, 120],
            ['SKU-004', 'Kopi Kemasan', 3000, 4500, 80],
            ['SKU-005', 'Susu UHT 1L', 14000, 18000, 30],
            ['SKU-006', 'Teh Botol 450ml', 3000, 4500, 80],
            ['SKU-007', 'Air Mineral 1.5L', 4000, 6000, 60],
            ['SKU-008', 'Biskuit Cokelat', 6500, 9000, 50],
            ['SKU-009', 'Keripik Kentang', 8000, 11000, 40],
            ['SKU-010', 'Gula Pasir 1kg', 14500, 18000, 35],
            ['SKU-011', 'Minyak Goreng 1L', 16000, 20000, 40],
            ['SKU-012', 'Beras 5kg', 65000, 75000, 25],
            ['SKU-013', 'Telur Ayam 1kg', 26000, 32000, 30],
            ['SKU-014', 'Sabun Mandi', 3500, 5000, 70],
            ['SKU-015', 'Sampo 170ml', 14000, 18000, 35],
            ['SKU-016', 'Pasta Gigi', 9000, 12000, 40],
            ['SKU-017', 'Tisu Wajah', 7000, 10000, 50],
            ['SKU-018', 'Detergen 800g', 18000, 23000, 30],
            ['SKU-019', 'Sarden Kaleng', 12500, 16000, 35],
            ['SKU-020', 'Kecap Manis 275ml', 11000, 14500, 30],
        ] as [$kode, $nama, $hargaBeli, $hargaJual, $stok]) {
            Barang::firstOrCreate(
                ['kode_barang' => $kode],
                [
                    'id_admin' => $admin->id_admin,
                    'nama_barang' => $nama,
                    'harga_beli' => $hargaBeli,
                    'harga_jual' => $hargaJual,
                    'stok' => $stok,
                ],
            );
        }
    }
}
