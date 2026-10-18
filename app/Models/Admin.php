<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    // @dbml-sync:table-property
    protected $table = 'Admin';
    protected $primaryKey = 'id_admin';
    public $timestamps = false;
    // @enddbml-sync:table-property

    // @dbml-sync:fillable
    protected $fillable = [
        'nama',
		'username',
		'password',
		'no_telp'
    ];
    // @enddbml-sync:fillable

    protected $hidden = [
        'password',
    ];

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