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

    public static function selImprimibles()
    {

        return Etiqueta::select('id AS value', 'nombre AS text')
            ->whereIn('id', [2,3,4])
            ->orderBy('nombre', 'asc')
            ->get();

    }

    public static function selEtiquetas()
    {

        return Etiqueta::select('id AS value', 'nombre AS text')
            ->orderBy('nombre', 'asc')
            ->get();

    }
}
