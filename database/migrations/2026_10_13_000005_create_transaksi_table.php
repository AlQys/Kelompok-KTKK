<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Transaksi', function (Blueprint $table) {
            $table->increments('id_transaksi');
            $table->dateTime('tanggal_transaksi')->nullable();
            $table->unsignedInteger('id_kasir')->nullable();
            $table->foreign('id_kasir')->references('id_kasir')->on('Kasir')->onDelete('set null');
            $table->unsignedInteger('id_pelanggan')->nullable();
            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('Pelanggan')->onDelete('set null');
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->decimal('uang_bayar', 12, 2)->default(0);
            $table->decimal('kembalian', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Transaksi');
    }
};

