<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tipo extends Model
{
    use SoftDeletes;
    /**
     *
     * @return Array formateado para select Vuetify
     *
     */
    public static function selTiposCom()
        {
            return Tipo::select('id AS value', 'nombre AS text')
                ->where('id','<=','2')
                ->orderBy('nombre', 'asc')
                ->get();

        }

    public static function selTiposSoloCompras()
    {
        return Tipo::select('id AS value', 'nombre AS text')
            ->where('id','<=','1')
            ->orderBy('nombre', 'asc')
            ->get();

    }

    public static function selTiposVen()
    {
        return Tipo::select('id AS value', 'nombre AS text')
            ->where('id','>=','3')
            ->orderBy('nombre', 'asc')
            ->get();

    }

    public static function selTiposWithContador()
    {
        return Tipo::select('tipo_id AS value', 'nombre AS text')
            ->join('contadores', 'tipos.id', '=', 'contadores.tipo_id')
            ->where('tipos.id','>=','3')
            ->where('empresa_id','=',session('empresa_id'))
            ->where('ejercicio','=',date('Y'))
            ->orderBy('nombre', 'asc')
            ->get();

    }
}
