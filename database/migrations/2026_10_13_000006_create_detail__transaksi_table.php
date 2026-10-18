<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Detail_Transaksi', function (Blueprint $table) {
            $table->increments('id_detail');
            $table->unsignedInteger('id_transaksi')->nullable();
            $table->foreign('id_transaksi')->references('id_transaksi')->on('Transaksi')->onDelete('cascade');
            $table->unsignedInteger('id_barang')->nullable();
            $table->foreign('id_barang')->references('id_barang')->on('Barang')->onDelete('restrict');
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_satuan', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Detail_Transaksi');
    }
};

