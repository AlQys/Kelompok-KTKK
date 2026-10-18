<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminSeeder::class);
        $this->call(OwnerSeeder::class);
        $this->call(KasirSeeder::class);
        $this->call(PelangganSeeder::class);
        $this->call(BarangSeeder::class);
        $this->call(TransaksiSeeder::class);

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password'],
        );
    }
}
