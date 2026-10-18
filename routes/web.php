<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::get('login', [SessionController::class, 'create'])->name('login');
Route::post('login', [SessionController::class, 'store'])->middleware('throttle:5,1')->name('login.store');

Route::middleware('pos.auth')->group(function (): void {
    Route::post('logout', [SessionController::class, 'destroy'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::middleware('pos.role:admin')->group(function (): void {
        Route::resource('barang', BarangController::class)->except('show');
        Route::resource('pelanggan', PelangganController::class)->except('show');
    });

    Route::middleware('pos.role:admin,kasir')->get(
        'pelanggan/search',
        [PelangganController::class, 'search'],
    )->name('pelanggan.search');

    Route::middleware('pos.role:admin,kasir')->group(function (): void {
        Route::resource('transaksi', TransaksiController::class)->only(['create', 'store']);
    });

    Route::middleware('pos.role:admin,kasir,owner')->group(function (): void {
        Route::resource('transaksi', TransaksiController::class)->only(['index', 'show']);
    });
});
