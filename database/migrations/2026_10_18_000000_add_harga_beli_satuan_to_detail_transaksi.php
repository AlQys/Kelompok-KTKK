<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Detail_Transaksi', function (Blueprint $table): void {
            $table->decimal('harga_beli_satuan', 12, 2)->nullable()->after('harga_satuan');
        });
    }

    public function down(): void
    {
        Schema::table('Detail_Transaksi', function (Blueprint $table): void {
            $table->dropColumn('harga_beli_satuan');
        });
    }
};
