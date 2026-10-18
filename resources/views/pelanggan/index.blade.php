@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('content')
    <div class="page-head">
        <div><h1>Pelanggan</h1><div class="muted">Cari dan kelola data pelanggan.</div></div>
        <a class="button" href="{{ route('pelanggan.create') }}">Tambah pelanggan</a>
    </div>
    <section class="card">
        <form method="get" class="actions" style="margin-bottom:16px">
            <input name="q" value="{{ $search }}" placeholder="Cari nama atau nomor telepon" aria-label="Cari pelanggan">
            <button type="submit">Cari</button>
            @if ($search !== '')<a class="button secondary" href="{{ route('pelanggan.index') }}">Reset</a>@endif
        </form>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Nama</th><th>Nomor telepon</th><th>Jumlah transaksi</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse ($pelanggans as $pelanggan)
                    <tr>
                        <td>{{ $pelanggan->nama }}</td><td>{{ $pelanggan->no_telp ?: '-' }}</td><td>{{ number_format($pelanggan->transaksis_count) }}</td>
                        <td class="actions">
                            <a href="{{ route('pelanggan.edit', $pelanggan) }}">Ubah</a>
                            <form class="inline" method="post" action="{{ route('pelanggan.destroy', $pelanggan) }}" onsubmit="return confirm('Hapus pelanggan ini?')">
                                @csrf @method('delete')<button class="danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">Belum ada pelanggan yang cocok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $pelanggans->links() }}</div>
    </section>
@endsection
