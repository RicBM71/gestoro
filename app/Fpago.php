<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Fpago extends Model
{
    protected $fillable = [
        'nombre','username'
    ];

    /**
     *
     * @return Array formateado para select Vuetify
     *
     */
    public static function selFPagos()
    {

        return Fpago::select('id AS value', 'nombre AS text')
            ->orderBy('nombre', 'asc')
            ->get();

        // return DB::table('fpagos')->select('id AS field', 'nombre AS text')
        //             ->orderBy('nombre', 'asc')
        //             ->get();
    }

    public function setNombreAttribute($nombre)
    {
        $this->attributes['nombre'] = strtoupper($nombre);

    }
}
