<?php

namespace App;

use App\Scopes\EmpresaScope;
use Illuminate\Database\Eloquent\Model;

class Apunte extends Model
{
    protected $fillable = [
        'empresa_id','nombre', 'username', 'color'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaScope);
    }

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

    // public static function selApuntesUser()
    // {

    //     return Apunte::select('id AS value', 'nombre AS text')
    //         ->where('id', '>', 30)
    //         ->orderBy('nombre', 'asc')
    //         ->get();
    // }


    // public static function scopeLibres($query){

    //     return $query->where('id', '>', 30);

    // }



}
