<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
    public static function selEstados()
    {

        return Etiqueta::select('id AS value', 'nombre AS text')
            ->orderBy('nombre', 'asc')
            ->get();

    }
}
