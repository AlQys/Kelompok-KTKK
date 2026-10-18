<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    // @dbml-sync:table-property
    protected $table = 'Owner';
    // @enddbml-sync:table-property

    protected $primaryKey = 'id_owner';

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

    // @enddbml-sync:relations
}
