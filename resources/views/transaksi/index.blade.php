@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    <div class="page-head">
        <div><h1>Transaksi</h1><div class="muted">Riwayat transaksi penjualan.</div></div>
        @if (in_array(session('pos_role'), ['admin', 'kasir'], true))
            <a class="button" href="{{ route('transaksi.create') }}">Buat transaksi</a>
        @endif
    </div>
    <section class="card">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Nomor</th><th>Waktu</th><th>Kasir</th><th>Pelanggan</th><th>Total</th><th></th></tr></thead>
                <tbody>
                @forelse ($transaksis as $transaksi)
                    <tr>
                        <td>{{ $transaksi->kode_transaksi ?? '#'.$transaksi->id_transaksi }}</td><td>{{ $transaksi->tanggal_transaksi?->format('d/m/Y H:i') }}</td>
                        <td>{{ $transaksi->kasir?->nama ?? '-' }}</td><td>{{ $transaksi->pelanggan?->nama ?? 'Umum' }}</td>
                        <td>Rp{{ number_format((float) $transaksi->total_harga, 0, ',', '.') }}</td>
                        <td><a href="{{ route('transaksi.show', $transaksi) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada transaksi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $transaksis->links() }}</div>
    </section>
@endsection
