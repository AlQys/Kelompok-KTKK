<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    // @dbml-sync:table-property
    protected $table = 'Transaksi';
    // @enddbml-sync:table-property

    protected $primaryKey = 'id_transaksi';

    public $timestamps = false;

    // @dbml-sync:fillable
    protected $fillable = [
        'kode_transaksi',
        'tanggal_transaksi',
        'id_kasir',
        'id_pelanggan',
        'total_harga',
        'uang_bayar',
        'kembalian',
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'total_harga' => 'decimal:2',
        'uang_bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function kasir()
    {
        return $this->belongsTo(Kasir::class, 'id_kasir', 'id_kasir');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    // @enddbml-sync:relations
}
