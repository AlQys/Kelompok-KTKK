@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="page-head">
        <div><h1>Transaksi {{ $transaksi->kode_transaksi ?? '#'.$transaksi->id_transaksi }}</h1><div class="muted">{{ $transaksi->tanggal_transaksi?->format('d/m/Y H:i') }}</div></div>
        <a class="button secondary" href="{{ route('transaksi.index') }}">Kembali</a>
    </div>
    <section class="card">
        <div class="grid">
            <div><div class="muted">Kasir</div><strong>{{ $transaksi->kasir?->nama ?? '-' }}</strong></div>
            <div><div class="muted">Pelanggan</div><strong>{{ $transaksi->pelanggan?->nama ?? 'Umum' }}</strong></div>
        </div>
    </section>
    <section class="card">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Barang</th><th>Jumlah</th><th>Harga satuan</th><th>Subtotal</th></tr></thead>
                <tbody>
                @foreach ($transaksi->detailTransaksis as $detail)
                    <tr><td>{{ $detail->barang?->nama_barang ?? 'Barang dihapus' }}</td><td>{{ $detail->jumlah }}</td><td>Rp{{ number_format((float) $detail->harga_satuan, 0, ',', '.') }}</td><td>Rp{{ number_format((float) $detail->subtotal, 0, ',', '.') }}</td></tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr><th colspan="3">Total</th><th>Rp{{ number_format((float) $transaksi->total_harga, 0, ',', '.') }}</th></tr>
                    <tr><th colspan="3">Dibayar</th><th>Rp{{ number_format((float) $transaksi->uang_bayar, 0, ',', '.') }}</th></tr>
                    <tr><th colspan="3">Kembalian</th><th>Rp{{ number_format((float) $transaksi->kembalian, 0, ',', '.') }}</th></tr>
                </tfoot>
            </table>
        </div>
    </section>
@endsection
