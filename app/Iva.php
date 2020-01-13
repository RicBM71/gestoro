<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Iva extends Model
{
    protected $fillable = [
        'nombre', 'rebu', 'importe', 'leyenda', 'username'
    ];

    public static function selIvas()
    {

        return Iva::select('id AS value', 'nombre AS text')
            ->orderBy('nombre', 'asc')
            ->get();

    }
}
