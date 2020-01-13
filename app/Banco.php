<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $fillable = [
        'entidad', 'nombre', 'bic'
    ];

    public static function selEntidadBic(){

        return Banco::select('entidad', 'bic')
            ->orderBy('entidad', 'asc')
            ->get();

    }

}
