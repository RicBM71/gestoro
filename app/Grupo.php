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

    public function scopeRebu($query){

        return $query->where('rebu', true);

    }


}
