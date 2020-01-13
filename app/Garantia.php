<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Garantia extends Model
{

    protected $fillable = [
        'nombre','leyenda', 'username'
    ];

    public static function selGarantias(){

        return Garantia::select('id AS value', 'nombre AS text')
            ->orderBy('nombre', 'asc')
            ->get();

    }
}
