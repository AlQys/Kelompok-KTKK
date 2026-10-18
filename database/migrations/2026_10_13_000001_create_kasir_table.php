<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Kasir', function (Blueprint $table) {
            $table->increments('id_kasir');
            $table->string('nama', 100);
            $table->string('username', 50);
            $table->string('password', 255);
            $table->string('no_telp', 15)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Kasir');
    }
};

