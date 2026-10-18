@extends('layouts.app')

@section('title', 'Ringkasan POS')

@section('content')
    <div class="page-head">
        <div><h1>Ringkasan</h1><div class="muted">Status data operasional toko.</div></div>
        @if (in_array(session('pos_role'), ['admin', 'kasir'], true))
            <a class="button" href="{{ route('transaksi.create') }}">Buat transaksi</a>
        @endif
    </div>
    <section class="cards">
        <div class="card card-stat"><div class="muted">Barang</div><div class="metric">{{ number_format($productCount) }}</div></div>
        <div class="card card-stat"><div class="muted">Pelanggan</div><div class="metric">{{ number_format($customerCount) }}</div></div>
        <div class="card card-stat"><div class="muted">Kasir</div><div class="metric">{{ number_format($cashierCount) }}</div></div>
        <div class="card card-stat"><div class="muted">Transaksi</div><div class="metric">{{ number_format($transactionCount) }}</div></div>
        <div class="card card-financial"><div class="muted">Total penjualan</div><div class="metric metric-currency">Rp{{ number_format($salesTotal, 0, ',', '.') }}</div></div>
        <div class="card card-financial"><div class="muted">Estimasi laba kotor</div><div class="metric metric-currency">Rp{{ number_format($profitTotal, 0, ',', '.') }}</div><small class="muted">Dihitung dari transaksi dengan harga modal tersimpan.</small></div>
    </section>
    <section class="card">
        <h2>Barang terlaris</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Barang</th><th>Jumlah terjual</th></tr></thead>
                <tbody>
                @forelse ($topProducts as $product)
                    <tr><td>{{ $product->nama_barang }}</td><td>{{ number_format($product->terjual) }}</td></tr>
                @empty
                    <tr><td colspan="2" class="empty">Belum ada data penjualan barang.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="card">
        <h2>Transaksi terbaru</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Nomor</th><th>Waktu</th><th>Kasir</th><th>Pelanggan</th><th>Total</th><th></th></tr></thead>
                <tbody>
                @forelse ($recentTransactions as $transaksi)
                    <tr>
                        <td>{{ $transaksi->kode_transaksi ?? '#'.$transaksi->id_transaksi }}</td>
                        <td>{{ $transaksi->tanggal_transaksi?->format('d/m/Y H:i') }}</td>
                        <td>{{ $transaksi->kasir?->nama ?? '-' }}</td>
                        <td>{{ $transaksi->pelanggan?->nama ?? 'Umum' }}</td>
                        <td>Rp{{ number_format((float) $transaksi->total_harga, 0, ',', '.') }}</td>
                        <td><a href="{{ route('transaksi.show', $transaksi) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada transaksi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
