<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $fillable = [
        'nombre','rebu', 'username', 'leyenda'
    ];

    public static function selGrupos(){

        return Grupo::select('id AS value', 'nombre AS text')
            ->orderBy('nombre', 'asc')
            ->get();

    }

    public static function selGruposRebu(){

        return Grupo::select('id AS value', 'nombre AS text')
            ->rebu()
            ->orderBy('nombre', 'asc')
            ->get();

    }

    public static function selGruposEmpresa(){

        return Grupo::select('grupos.id AS value', 'grupos.nombre AS text')
            ->join('libros','libros.grupo_id','=','grupos.id')
            ->rebu()
            ->where('empresa_id',session('empresa_id'))
            ->orderBy('grupos.nombre', 'asc')
            ->distinct()
            ->get();

    }

    public function scopeRebu($query){

        return $query->where('rebu', true);

    }


}
