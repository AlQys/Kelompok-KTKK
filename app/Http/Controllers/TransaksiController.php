<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kasir;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransaksiController extends Controller
{
    private const MAX_MONEY_CENTS = 999_999_999_999;

    public function index(): View
    {
        return view('transaksi.index', [
            'transaksis' => Transaksi::with(['kasir', 'pelanggan'])
                ->latest('tanggal_transaksi')
                ->paginate(25),
        ]);
    }

    public function create(): View
    {
        $barangs = Barang::where('stok', '>', 0)->orderBy('nama_barang')->get();

        return view('transaksi.create', [
            'barangs' => $barangs,
            'products' => $barangs->map(fn (Barang $barang): array => [
                'id' => $barang->id_barang,
                'name' => $barang->nama_barang,
                'code' => $barang->kode_barang,
                'price' => $barang->harga_jual,
                'stock' => $barang->stok,
            ])->values(),
            'kasirs' => session('pos_role') === 'kasir'
                ? Kasir::whereKey(session('pos_user_id'))->get()
                : Kasir::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_kasir' => session('pos_role') === 'kasir'
                ? ['nullable']
                : ['required', 'integer', 'exists:Kasir,id_kasir'],
            'id_pelanggan' => ['nullable', 'integer', 'exists:Pelanggan,id_pelanggan'],
            'uang_bayar' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_barang' => ['required', 'integer', 'distinct', 'exists:Barang,id_barang'],
            'items.*.jumlah' => ['required', 'integer', 'min:1', 'max:2147483647'],
        ]);

        $transaksi = DB::transaction(function () use ($data): Transaksi {
            $kasirId = session('pos_role') === 'kasir'
                ? session('pos_user_id')
                : $data['id_kasir'];
            $requestedItems = collect($data['items'])->keyBy('id_barang');
            $barangs = Barang::whereIn('id_barang', $requestedItems->keys())
                ->orderBy('id_barang')
                ->lockForUpdate()
                ->get()
                ->keyBy('id_barang');

            if ($barangs->count() !== $requestedItems->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Salah satu barang tidak lagi tersedia.',
                ]);
            }

            $lines = [];
            $totalCents = 0;

            foreach ($requestedItems as $idBarang => $item) {
                $barang = $barangs->get($idBarang);
                $jumlah = (int) $item['jumlah'];

                if ($barang->stok < $jumlah) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$barang->nama_barang} tidak mencukupi.",
                    ]);
                }

                if ($barang->harga_jual === null || $barang->harga_beli === null) {
                    throw ValidationException::withMessages([
                        'items' => "Harga {$barang->nama_barang} belum lengkap.",
                    ]);
                }

                $unitCents = (int) round((float) $barang->harga_jual * 100);

                if ($unitCents > intdiv(self::MAX_MONEY_CENTS, $jumlah)) {
                    throw ValidationException::withMessages([
                        'items' => "Nilai transaksi untuk {$barang->nama_barang} melebihi batas.",
                    ]);
                }

                $subtotalCents = $unitCents * $jumlah;

                if ($totalCents > self::MAX_MONEY_CENTS - $subtotalCents) {
                    throw ValidationException::withMessages([
                        'items' => 'Total transaksi melebihi batas nilai yang didukung.',
                    ]);
                }

                $totalCents += $subtotalCents;
                $lines[] = [
                    'barang' => $barang,
                    'jumlah' => $jumlah,
                    'harga_satuan' => number_format($unitCents / 100, 2, '.', ''),
                    'harga_beli_satuan' => number_format((int) round((float) $barang->harga_beli * 100) / 100, 2, '.', ''),
                    'subtotal' => number_format($subtotalCents / 100, 2, '.', ''),
                ];
            }

            $paymentCents = (int) round((float) $data['uang_bayar'] * 100);

            if ($paymentCents < $totalCents) {
                throw ValidationException::withMessages([
                    'uang_bayar' => 'Uang bayar tidak boleh kurang dari total transaksi.',
                ]);
            }

            $transaksi = Transaksi::create([
                'kode_transaksi' => 'POS-'.now()->format('YmdHis').'-'.strtoupper(bin2hex(random_bytes(3))),
                'tanggal_transaksi' => now(),
                'id_kasir' => $kasirId,
                'id_pelanggan' => $data['id_pelanggan'] ?? null,
                'total_harga' => number_format($totalCents / 100, 2, '.', ''),
                'uang_bayar' => number_format($paymentCents / 100, 2, '.', ''),
                'kembalian' => number_format(($paymentCents - $totalCents) / 100, 2, '.', ''),
            ]);

            foreach ($lines as $line) {
                $transaksi->detailTransaksis()->create([
                    'id_barang' => $line['barang']->id_barang,
                    'jumlah' => $line['jumlah'],
                    'harga_satuan' => $line['harga_satuan'],
                    'harga_beli_satuan' => $line['harga_beli_satuan'],
                    'subtotal' => $line['subtotal'],
                ]);

                $line['barang']->decrement('stok', $line['jumlah']);
            }

            return $transaksi;
        });

        return to_route('transaksi.show', $transaksi)->with('status', 'Transaksi berhasil disimpan.');
    }

    public function show(Transaksi $transaksi): View
    {
        return view('transaksi.show', [
            'transaksi' => $transaksi->load(['kasir', 'pelanggan', 'detailTransaksis.barang']),
        ]);
    }
}
