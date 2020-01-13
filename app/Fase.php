<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fase extends Model
{
    public static function selFases($comven="C"){

        return Fase::select('id AS value', 'nombre AS text')
            ->tipo($comven)
            ->orderBy('nombre', 'asc')
            ->get();

    }

    public function scopeTipo($query, $comven){

        return $query->where('comven', '=', $comven);

    }

    public function getNombreAttribute(){
        return strtoUpper($this->attributes['nombre']);
    }

}
