<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kasir;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'productCount' => Barang::count(),
            'customerCount' => Pelanggan::count(),
            'cashierCount' => Kasir::count(),
            'transactionCount' => Transaksi::count(),
            'salesTotal' => (float) Transaksi::sum('total_harga'),
            'profitTotal' => (float) DB::table('Detail_Transaksi')
                ->whereNotNull('harga_beli_satuan')
                ->selectRaw('COALESCE(SUM((harga_satuan - harga_beli_satuan) * jumlah), 0) as total')
                ->value('total'),
            'topProducts' => DB::table('Detail_Transaksi')
                ->join('Barang', 'Detail_Transaksi.id_barang', '=', 'Barang.id_barang')
                ->select('Barang.nama_barang')
                ->selectRaw('SUM(Detail_Transaksi.jumlah) as terjual')
                ->groupBy('Barang.id_barang', 'Barang.nama_barang')
                ->orderByDesc('terjual')
                ->limit(5)
                ->get(),
            'recentTransactions' => Transaksi::with(['kasir', 'pelanggan'])
                ->latest('tanggal_transaksi')
                ->limit(10)
                ->get(),
        ]);
    }
}
