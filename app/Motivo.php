<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Motivo extends Model
{

    protected $fillable = ['nombre','username'];

    public static function selMotivos()
    {

        return Motivo::select('id AS value', 'nombre AS text')
            ->orderBy('nombre', 'asc')
            ->get();

    }

}
