<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    // @dbml-sync:table-property
    protected $table = 'Pelanggan';
    // @enddbml-sync:table-property

    protected $primaryKey = 'id_pelanggan';

    public $timestamps = false;

    // @dbml-sync:fillable
    protected $fillable = [
        'nama',
        'no_telp',
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_pelanggan', 'id_pelanggan');
    }

    // @enddbml-sync:relations
}
