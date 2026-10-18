<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Akun Admin default / master untuk login/testing
        Admin::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator Utama',
                'password' => Hash::make('admin123'),
                'no_telp' => '081234567890',
            ]
        );

        // Generate 5 data dummy admin menggunakan AdminFactory
        Admin::factory()->count(5)->create();
    }
}

