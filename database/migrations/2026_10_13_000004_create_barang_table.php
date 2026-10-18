<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Barang', function (Blueprint $table) {
            $table->increments('id_barang');
            $table->unsignedInteger('id_admin')->nullable();
            $table->foreign('id_admin')->references('id_admin')->on('Admin')->onDelete('set null');
            $table->string('kode_barang', 20)->unique();
            $table->string('nama_barang', 100);
            $table->decimal('harga_beli', 12, 2)->default(0);
            $table->decimal('harga_jual', 12, 2)->default(0);
            $table->integer('stok')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Barang');
    }
};

