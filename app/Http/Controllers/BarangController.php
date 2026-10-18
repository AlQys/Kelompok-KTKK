<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Barang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        return view('barang.index', [
            'barangs' => Barang::with('admin')
                ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                    $query->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%");
                }))
                ->orderBy('nama_barang')
                ->paginate(25)
                ->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('barang.form', [
            'barang' => new Barang,
            'admins' => Admin::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Barang::create($this->validatedData($request));

        return to_route('barang.index')->with('status', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang): View
    {
        return view('barang.form', [
            'barang' => $barang,
            'admins' => Admin::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Barang $barang): RedirectResponse
    {
        $barang->update($this->validatedData($request, $barang));

        return to_route('barang.index')->with('status', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang): RedirectResponse
    {
        if ($barang->detailTransaksis()->exists()) {
            return to_route('barang.index')->withErrors([
                'barang' => 'Barang yang sudah tercatat pada transaksi tidak dapat dihapus.',
            ]);
        }

        $barang->delete();

        return to_route('barang.index')->with('status', 'Barang berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Barang $barang = null): array
    {
        return $request->validate([
            'id_admin' => ['required', 'integer', 'exists:Admin,id_admin'],
            'kode_barang' => [
                'required',
                'string',
                'max:20',
                Rule::unique('Barang', 'kode_barang')->ignore($barang?->id_barang, 'id_barang'),
            ],
            'nama_barang' => ['required', 'string', 'max:100'],
            'harga_beli' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'harga_jual' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'stok' => ['required', 'integer', 'min:0'],
        ]);
    }
}
