<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kasir;
use App\Models\Pelanggan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransaksiSeeder extends Seeder
{
    private const TARGET_COUNT = 100_000;

    private const TRANSACTIONS_PER_BATCH = 100;

    private const DETAILS_PER_BATCH = 160;

    public function run(): void
    {
        $kasirIds = Kasir::orderBy('id_kasir')->pluck('id_kasir')->all();
        $barangs = Barang::orderBy('id_barang')->get();
        $pelangganIds = Pelanggan::orderBy('id_pelanggan')->pluck('id_pelanggan')->all();

        if ($kasirIds === [] || $barangs->isEmpty() || $pelangganIds === []) {
            throw new \RuntimeException('Seed transaksi membutuhkan kasir, barang, dan pelanggan.');
        }

        $baseDate = now()->subDays(180);
        $productCount = $barangs->count();
        $customerCount = count($pelangganIds);
        $cashierCount = count($kasirIds);

        for ($firstNumber = 1; $firstNumber <= self::TARGET_COUNT; $firstNumber += self::TRANSACTIONS_PER_BATCH) {
            $lastNumber = min($firstNumber + self::TRANSACTIONS_PER_BATCH - 1, self::TARGET_COUNT);
            $codes = [];

            for ($number = $firstNumber; $number <= $lastNumber; $number++) {
                $codes[] = sprintf('DEMO-%06d', $number);
            }

            $existingTransactions = DB::table('Transaksi')
                ->whereIn('kode_transaksi', $codes)
                ->get(['kode_transaksi', 'id_kasir', 'id_pelanggan'])
                ->keyBy('kode_transaksi');

            DB::transaction(function () use (
                $firstNumber,
                $lastNumber,
                $existingTransactions,
                $kasirIds,
                $barangs,
                $pelangganIds,
                $baseDate,
                $productCount,
                $customerCount,
                $cashierCount,
            ): void {
                $transactions = [];
                $detailsByCode = [];

                for ($number = $firstNumber; $number <= $lastNumber; $number++) {
                    $kodeTransaksi = sprintf('DEMO-%06d', $number);

                    if ($existingTransactions->has($kodeTransaksi)) {
                        $existing = $existingTransactions->get($kodeTransaksi);
                        $updates = [];

                        if ($existing->id_kasir === null) {
                            $updates['id_kasir'] = $kasirIds[($number - 1) % $cashierCount];
                        }

                        if ($existing->id_pelanggan === null) {
                            $updates['id_pelanggan'] = $pelangganIds[($number - 1) % $customerCount];
                        }

                        if ($updates !== []) {
                            DB::table('Transaksi')
                                ->where('kode_transaksi', $kodeTransaksi)
                                ->update($updates);
                        }

                        continue;
                    }

                    $lineCount = min((($number - 1) % 4) + 1, $productCount);
                    $lines = [];
                    $total = 0;

                    for ($line = 0; $line < $lineCount; $line++) {
                        $barang = $barangs->get((($number - 1 + $line) % $productCount));
                        $jumlah = (($number + $line) % 4) + 1;
                        $hargaSatuan = (int) round((float) $barang->harga_jual);
                        $hargaBeliSatuan = (int) round((float) $barang->harga_beli);
                        $subtotal = $hargaSatuan * $jumlah;
                        $total += $subtotal;

                        $lines[] = [
                            'id_barang' => $barang->id_barang,
                            'jumlah' => $jumlah,
                            'harga_satuan' => $hargaSatuan,
                            'harga_beli_satuan' => $hargaBeliSatuan,
                            'subtotal' => $subtotal,
                        ];
                    }

                    $uangBayar = (int) (ceil($total / 10_000) * 10_000);
                    $transactions[] = [
                        'kode_transaksi' => $kodeTransaksi,
                        'tanggal_transaksi' => $baseDate->copy()
                            ->addSeconds(($number * 157) % (180 * 24 * 60 * 60))
                            ->format('Y-m-d H:i:s'),
                        'id_kasir' => $kasirIds[($number - 1) % $cashierCount],
                        'id_pelanggan' => $pelangganIds[($number - 1) % $customerCount],
                        'total_harga' => $total,
                        'uang_bayar' => $uangBayar,
                        'kembalian' => $uangBayar - $total,
                    ];
                    $detailsByCode[$kodeTransaksi] = $lines;
                }

                if ($transactions === []) {
                    return;
                }

                DB::table('Transaksi')->insert($transactions);

                $transactionIds = DB::table('Transaksi')
                    ->whereIn('kode_transaksi', array_keys($detailsByCode))
                    ->pluck('id_transaksi', 'kode_transaksi');
                $details = [];

                foreach ($detailsByCode as $kodeTransaksi => $lines) {
                    foreach ($lines as $line) {
                        $details[] = ['id_transaksi' => $transactionIds[$kodeTransaksi]] + $line;
                    }
                }

                foreach (array_chunk($details, self::DETAILS_PER_BATCH) as $detailBatch) {
                    DB::table('Detail_Transaksi')->insert($detailBatch);
                }
            });
        }
    }
}
