@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
    <div class="page-head">
        <div><h1>Barang</h1><div class="muted">Kelola katalog dan stok produk.</div></div>
        <a class="button" href="{{ route('barang.create') }}">Tambah barang</a>
    </div>
    <section class="card">
        <form method="get" class="actions" style="margin-bottom:16px">
            <input name="q" value="{{ $search }}" placeholder="Cari nama atau kode barang" aria-label="Cari barang">
            <button type="submit">Cari</button>
            @if ($search !== '')<a class="button secondary" href="{{ route('barang.index') }}">Reset</a>@endif
        </form>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Kode</th><th>Nama</th><th>Harga beli</th><th>Harga jual</th><th>Stok</th><th>Admin</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse ($barangs as $barang)
                    <tr>
                        <td>{{ $barang->kode_barang }}</td><td>{{ $barang->nama_barang }}</td>
                        <td>Rp{{ number_format((float) $barang->harga_beli, 0, ',', '.') }}</td>
                        <td>Rp{{ number_format((float) $barang->harga_jual, 0, ',', '.') }}</td>
                        <td>{{ number_format($barang->stok) }}</td><td>{{ $barang->admin?->nama ?? '-' }}</td>
                        <td class="actions">
                            <a href="{{ route('barang.edit', $barang) }}">Ubah</a>
                            <form class="inline" method="post" action="{{ route('barang.destroy', $barang) }}" onsubmit="return confirm('Hapus barang ini?')">
                                @csrf @method('delete')<button class="danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada barang yang cocok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $barangs->links() }}</div>
    </section>
@endsection
