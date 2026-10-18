<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelangganController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q'));

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        return response()->json(
            Pelanggan::query()
                ->where(function ($builder) use ($query): void {
                    $builder->where('nama', 'like', "%{$query}%")
                        ->orWhere('no_telp', 'like', "%{$query}%");
                })
                ->orderBy('nama')
                ->limit(20)
                ->get(['id_pelanggan', 'nama', 'no_telp']),
        );
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        return view('pelanggan.index', [
            'pelanggans' => Pelanggan::withCount('transaksis')
                ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('no_telp', 'like', "%{$search}%");
                }))
                ->orderBy('nama')
                ->paginate(50)
                ->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('pelanggan.form', ['pelanggan' => new Pelanggan]);
    }

    public function store(Request $request): RedirectResponse
    {
        Pelanggan::create($this->validatedData($request));

        return to_route('pelanggan.index')->with('status', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Pelanggan $pelanggan): View
    {
        return view('pelanggan.form', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan): RedirectResponse
    {
        $pelanggan->update($this->validatedData($request));

        return to_route('pelanggan.index')->with('status', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan): RedirectResponse
    {
        if ($pelanggan->transaksis()->exists()) {
            return to_route('pelanggan.index')->withErrors([
                'pelanggan' => 'Pelanggan yang memiliki riwayat transaksi tidak dapat dihapus.',
            ]);
        }

        $pelanggan->delete();

        return to_route('pelanggan.index')->with('status', 'Pelanggan berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'no_telp' => ['nullable', 'string', 'max:15'],
        ]);
    }
}
