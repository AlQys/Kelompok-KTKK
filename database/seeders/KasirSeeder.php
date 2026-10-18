<?php

namespace Database\Seeders;

use App\Models\Kasir;
use Illuminate\Database\Seeder;

class KasirSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['kasir', 'Kasir Utama', '081234567892'],
            ['kasir2', 'Kasir Pagi', '081234567893'],
            ['kasir3', 'Kasir Sore', '081234567894'],
        ] as [$username, $nama, $noTelp]) {
            Kasir::updateOrCreate(
                ['username' => $username],
                [
                    'nama' => $nama,
                    'password' => 'password',
                    'no_telp' => $noTelp,
                ],
            );
        }
    }
}
