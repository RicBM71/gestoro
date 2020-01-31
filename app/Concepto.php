<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concepto extends Model
{
    public static function selConceptos($comven="C"){

        return Concepto::select('id AS value', 'nombre AS text')
            ->tipo($comven)
            ->orderBy('nombre', 'asc')
            ->get();

    }

    public function scopeTipo($query, $comven){

        return $query->where('comven', $comven);

    }

    public static function selConceptosDep(){

        return Concepto::select('id AS value', 'nombre AS text')
            ->where('id','<=',3)
            ->orderBy('nombre', 'asc')
            ->get();

    }

    public static function selConceptosC(){

        return Concepto::select('id AS value', 'nombre AS text')
            ->tipo('C')
            ->orderBy('id', 'asc');


    }

    // public static function selConceptosV(){

    //     return Concepto::select('id AS value', 'nombre AS text')
    //         ->tipo('V')
    //         ->orderBy('id', 'asc');


    // }


    public function setNombreAttribute($nombre)
    {
        $this->attributes['nombre'] = strtoupper($nombre);

    }


    public function scopeDepositos($query){

        return $query->whereIn('id', [1,2,3]);

    }

    public function scopeAmpliaciones($query){

        return $query->whereIn('id', [4,5,6]);

    }

    public function scopeAcuenta($query){

        return $query->whereIn('id', [7,8,9]);

    }

    public function scopeRecuperar($query){

        return $query->whereIn('id', [10,11,12]);

    }

    public function scopeComprar($query){

        return $query->whereIn('id', [13,14,15]);

    }
    
    public function scopeCapital($query){

        return $query->whereIn('id', [16,17,18]);

    }


}
