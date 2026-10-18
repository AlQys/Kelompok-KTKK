<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        Owner::updateOrCreate(
            ['username' => 'owner'],
            [
                'nama' => 'Pemilik Toko',
                'password' => 'password',
                'no_telp' => '081234567891',
            ],
        );
    }
}
