<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Seed the admin account.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator',
                'password' => 'password',
                'no_telp' => '081234567890',
            ],
        );
    }
}
