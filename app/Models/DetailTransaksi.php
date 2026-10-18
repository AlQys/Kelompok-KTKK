<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    use HasFactory;

    // @dbml-sync:table-property
    protected $table = 'Detail_Transaksi';
    // @enddbml-sync:table-property

    protected $primaryKey = 'id_detail';

    public $timestamps = false;

    // @dbml-sync:fillable
    protected $fillable = [
        'id_transaksi',
        'id_barang',
        'jumlah',
        'harga_satuan',
        'harga_beli_satuan',
        'subtotal',
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'harga_beli_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    // @enddbml-sync:relations
}
