@extends('layouts.app')

@section('title', 'Buat Transaksi')

@section('content')
    <div class="page-head"><div><h1>Buat transaksi</h1><div class="muted">Total dan stok dihitung dan divalidasi di server.</div></div><a class="button secondary" href="{{ route('transaksi.index') }}">Kembali</a></div>
    @if ($kasirs->isEmpty() || $barangs->isEmpty())
        <section class="card">
            <p>Transaksi belum dapat dibuat karena belum tersedia kasir atau barang dengan stok. Jalankan <code>php artisan db:seed</code> untuk menyiapkan data awal.</p>
        </section>
    @else
        <form method="post" action="{{ route('transaksi.store') }}">
            @csrf
            <section class="card">
                <div class="grid">
                    @if (session('pos_role') === 'kasir')
                        <input type="hidden" name="id_kasir" value="{{ session('pos_user_id') }}">
                        <div class="field"><label>Kasir</label><input value="{{ $kasirs->first()?->nama }}" disabled></div>
                    @else
                        <div class="field"><label for="id_kasir">Kasir</label><select id="id_kasir" name="id_kasir" required>@foreach ($kasirs as $kasir)<option value="{{ $kasir->id_kasir }}" @selected(old('id_kasir') == $kasir->id_kasir)>{{ $kasir->nama }}</option>@endforeach</select></div>
                    @endif
                    <div class="field">
                        <label for="pelanggan-search">Pelanggan (opsional)</label>
                        <input id="pelanggan-search" type="search" autocomplete="off" placeholder="Cari nama atau nomor telepon">
                        <input id="id_pelanggan" type="hidden" name="id_pelanggan" value="{{ old('id_pelanggan') }}">
                        <div id="pelanggan-results" role="listbox"></div>
                        <small id="pelanggan-selected" class="muted">Pelanggan umum</small>
                    </div>
                </div>
            </section>
            <section class="card">
                <div class="page-head"><h2>Barang dibeli</h2><button class="secondary" type="button" id="add-item">Tambah baris</button></div>
                <div id="items"></div>
            </section>
            <section class="card">
                <div class="field"><label for="uang_bayar">Uang dibayar (Rp)</label><input id="uang_bayar" name="uang_bayar" type="number" min="0" step="0.01" value="{{ old('uang_bayar') }}" required></div>
                <button type="submit">Simpan transaksi</button>
            </section>
        </form>
    @endif
@endsection

@if ($kasirs->isNotEmpty() && $barangs->isNotEmpty())
    @push('scripts')
        <script>
            const products = @json($products);
            const items = document.getElementById('items');
            let row = 0;
            function addItem() {
                const index = row++;
                const line = document.createElement('div');
                line.className = 'grid item-row';
                line.style.alignItems = 'end';
                line.style.marginBottom = '12px';
                const productField = document.createElement('div');
                productField.className = 'field';
                const productLabel = document.createElement('label');
                productLabel.htmlFor = `barang-${index}`;
                productLabel.textContent = 'Barang';
                const productSelect = document.createElement('select');
                productSelect.id = `barang-${index}`;
                productSelect.name = `items[${index}][id_barang]`;
                productSelect.required = true;
                products.forEach(product => {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = `${product.code} — ${product.name} (stok ${product.stock}, Rp${Number(product.price).toLocaleString('id-ID')})`;
                    productSelect.append(option);
                });
                productField.append(productLabel, productSelect);

                const quantityField = document.createElement('div');
                quantityField.className = 'field';
                const quantityLabel = document.createElement('label');
                quantityLabel.htmlFor = `jumlah-${index}`;
                quantityLabel.textContent = 'Jumlah';
                const quantityInput = document.createElement('input');
                quantityInput.id = `jumlah-${index}`;
                quantityInput.name = `items[${index}][jumlah]`;
                quantityInput.type = 'number';
                quantityInput.min = '1';
                quantityInput.step = '1';
                quantityInput.value = '1';
                quantityInput.required = true;
                quantityField.append(quantityLabel, quantityInput);

                const removeButton = document.createElement('button');
                removeButton.className = 'danger';
                removeButton.type = 'button';
                removeButton.textContent = 'Hapus baris';
                removeButton.setAttribute('aria-label', 'Hapus baris');
                removeButton.addEventListener('click', () => {
                    if (items.children.length > 1) line.remove();
                });
                line.append(productField, quantityField, removeButton);
                items.append(line);
            }
            document.getElementById('add-item').addEventListener('click', addItem);
            addItem();

            const customerInput = document.getElementById('pelanggan-search');
            const customerId = document.getElementById('id_pelanggan');
            const customerResults = document.getElementById('pelanggan-results');
            const customerSelected = document.getElementById('pelanggan-selected');
            let customerSearchTimer;
            customerInput.addEventListener('input', () => {
                customerId.value = '';
                customerSelected.textContent = 'Pelanggan umum';
                customerResults.replaceChildren();
                clearTimeout(customerSearchTimer);
                const query = customerInput.value.trim();
                if (query.length < 2) return;
                customerSearchTimer = setTimeout(async () => {
                    try {
                        const response = await fetch(`{{ route('pelanggan.search') }}?q=${encodeURIComponent(query)}`, {
                            headers: { Accept: 'application/json' },
                        });
                        if (!response.ok) throw new Error('Pencarian pelanggan gagal.');
                        const matches = await response.json();
                        matches.forEach(customer => {
                            const option = document.createElement('button');
                            option.className = 'secondary';
                            option.type = 'button';
                            option.style.display = 'block';
                            option.style.marginTop = '6px';
                            option.textContent = `${customer.nama}${customer.no_telp ? ` — ${customer.no_telp}` : ''}`;
                            option.addEventListener('click', () => {
                                customerId.value = customer.id_pelanggan;
                                customerInput.value = customer.nama;
                                customerSelected.textContent = `Dipilih: ${customer.nama}`;
                                customerResults.replaceChildren();
                            });
                            customerResults.append(option);
                        });
                        if (matches.length === 0) customerResults.textContent = 'Pelanggan tidak ditemukan.';
                    } catch (error) {
                        customerResults.textContent = error.message;
                    }
                }, 250);
            });
        </script>
    @endpush
@endif
