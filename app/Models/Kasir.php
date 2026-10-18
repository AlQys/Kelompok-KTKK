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

    protected $primaryKey = 'id_kasir';

    public $timestamps = false;

    // @dbml-sync:fillable
    protected $fillable = [
        'nama',
        'username',
        'password',
        'no_telp',
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
        'password' => 'hashed',
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_kasir', 'id_kasir');
    }

    // @enddbml-sync:relations
}
