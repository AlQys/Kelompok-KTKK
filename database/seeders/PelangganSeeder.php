<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PelangganSeeder extends Seeder
{
    private const TARGET_COUNT = 100_000;

    private const BATCH_SIZE = 400;

    public function run(): void
    {
        $remaining = max(0, self::TARGET_COUNT - DB::table('Pelanggan')->count());
        $faker = fake();

        while ($remaining > 0) {
            $batchSize = min(self::BATCH_SIZE, $remaining);
            $customers = [];

            for ($i = 0; $i < $batchSize; $i++) {
                $customers[] = [
                    'nama' => $faker->name(),
                    'no_telp' => $faker->numerify('08##########'),
                ];
            }

            DB::table('Pelanggan')->insert($customers);
            $remaining -= $batchSize;
        }
    }
}
