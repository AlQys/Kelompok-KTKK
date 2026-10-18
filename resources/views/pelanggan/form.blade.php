@extends('layouts.app')

@section('title', $pelanggan->exists ? 'Ubah Pelanggan' : 'Tambah Pelanggan')

@section('content')
    <div class="page-head"><div><h1>{{ $pelanggan->exists ? 'Ubah pelanggan' : 'Tambah pelanggan' }}</h1></div><a class="button secondary" href="{{ route('pelanggan.index') }}">Kembali</a></div>
    <section class="card">
        <form method="post" action="{{ $pelanggan->exists ? route('pelanggan.update', $pelanggan) : route('pelanggan.store') }}">
            @csrf
            @if ($pelanggan->exists) @method('put') @endif
            <div class="field"><label for="nama">Nama</label><input id="nama" name="nama" maxlength="100" value="{{ old('nama', $pelanggan->nama) }}" required></div>
            <div class="field"><label for="no_telp">Nomor telepon</label><input id="no_telp" name="no_telp" maxlength="15" value="{{ old('no_telp', $pelanggan->no_telp) }}"></div>
            <button type="submit">Simpan</button>
        </form>
    </section>
@endsection
