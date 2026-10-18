@extends('layouts.app')

@section('title', $barang->exists ? 'Ubah Barang' : 'Tambah Barang')

@section('content')
    <div class="page-head"><div><h1>{{ $barang->exists ? 'Ubah barang' : 'Tambah barang' }}</h1></div><a class="button secondary" href="{{ route('barang.index') }}">Kembali</a></div>
    <section class="card">
        @if ($admins->isEmpty())
            <p class="muted">Belum ada admin. Jalankan <code>php artisan db:seed</code> terlebih dahulu.</p>
        @else
            <form method="post" action="{{ $barang->exists ? route('barang.update', $barang) : route('barang.store') }}">
                @csrf
                @if ($barang->exists) @method('put') @endif
                <div class="grid">
                    <div class="field"><label for="id_admin">Admin pengelola</label><select id="id_admin" name="id_admin" required>@foreach ($admins as $admin)<option value="{{ $admin->id_admin }}" @selected(old('id_admin', $barang->id_admin) == $admin->id_admin)>{{ $admin->nama }}</option>@endforeach</select></div>
                    <div class="field"><label for="kode_barang">Kode barang</label><input id="kode_barang" name="kode_barang" maxlength="20" value="{{ old('kode_barang', $barang->kode_barang) }}" required></div>
                    <div class="field"><label for="nama_barang">Nama barang</label><input id="nama_barang" name="nama_barang" maxlength="100" value="{{ old('nama_barang', $barang->nama_barang) }}" required></div>
                    <div class="field"><label for="harga_beli">Harga beli (Rp)</label><input id="harga_beli" name="harga_beli" type="number" min="0" step="0.01" value="{{ old('harga_beli', $barang->harga_beli) }}" required></div>
                    <div class="field"><label for="harga_jual">Harga jual (Rp)</label><input id="harga_jual" name="harga_jual" type="number" min="0" step="0.01" value="{{ old('harga_jual', $barang->harga_jual) }}" required></div>
                    <div class="field"><label for="stok">Stok</label><input id="stok" name="stok" type="number" min="0" step="1" value="{{ old('stok', $barang->stok ?? 0) }}" required></div>
                </div>
                <button type="submit">Simpan</button>
            </form>
        @endif
    </section>
@endsection
