<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Apunte extends Model
{
    protected $fillable = [
        'nombre', 'username', 'color'
    ];

      /**
     *
     * @return Array formateado para select Vuetify
     *
     */
    public static function selApuntes()
    {

        return Apunte::select('id AS value', 'nombre AS text')
            ->orderBy('nombre', 'asc')
            ->get();

    }

    public static function selApuntesUser()
    {

        return Apunte::select('id AS value', 'nombre AS text')
            ->where('id', '>', 30)
            ->orderBy('nombre', 'asc')
            ->get();
    }


    public static function scopeLibres($query){

        return $query->where('id', '>', 30);

    }



}
