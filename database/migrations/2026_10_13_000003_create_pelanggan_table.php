<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Pelanggan', function (Blueprint $table) {
            $table->increments('id_pelanggan');
            $table->string('nama', 100);
            $table->string('no_telp', 15)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Pelanggan');
    }
};

