<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Transaksi', function (Blueprint $table): void {
            $table->string('kode_transaksi', 30)->nullable()->unique()->after('id_transaksi');
        });
    }

    public function down(): void
    {
        Schema::table('Transaksi', function (Blueprint $table): void {
            $table->dropUnique(['kode_transaksi']);
            $table->dropColumn('kode_transaksi');
        });
    }
};
