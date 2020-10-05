<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $fillable = [
        'nombre', 'username'
    ];

    public static function selMarcas()
    {

        return Marca::select('id AS value', 'nombre AS text')
            ->orderBy('nombre', 'asc')
            ->get();

    }
}
