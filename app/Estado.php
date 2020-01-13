<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    public static function selEstados()
    {

        return Estado::select('id AS value', 'nombre AS text')
            ->where('activo', true)
            ->orderBy('nombre', 'asc')
            ->get();

    }
}
