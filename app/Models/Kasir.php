<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasir extends Model
{
    use HasFactory;

    // @dbml-sync:table-property
    protected $table = 'Kasir';
    // @enddbml-sync:table-property

    // @dbml-sync:fillable
    protected $fillable = [
        'nama',
		'username',
		'password',
		'no_telp'
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
        
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_kasir', 'id_kasir');
    }

    // @enddbml-sync:relations
}