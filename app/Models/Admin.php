<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    // @dbml-sync:table-property
    protected $table = 'Admin';
    // @enddbml-sync:table-property

    protected $primaryKey = 'id_admin';

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
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'id_admin', 'id_admin');
    }

    // @enddbml-sync:relations
}
