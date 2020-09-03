<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fixing extends Model
{
    protected $fillable = [
        'clase_id','fecha', 'importe','username',
    ];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
    ];

    /**
     * Si fecha albarán != false comprobamos que el albarán esté dentro del rango de comprobación
     * desde la primera fecha del fixing, de esta forma no controlamos albaranes antiguos para no liar.
     * 03/09/2020
     */

    public static function getFixDia($clase_id, $fecha, $fecha_albaran = false){

        if ($fecha == null) return 0;


        $data = Fixing::where('clase_id',$clase_id)
                      ->where('fecha', '<=', $fecha)
                      ->orderBy('fecha', 'desc')
                      ->first();

        if ($fecha_albaran === false || $data == false)
            return ($data == false) ? 0 : $data->importe;
        else{
            if ($fecha_albaran < $data->fecha)
                return false;
            else
                return ($data == false) ? 0 : $data->importe;

        }

    }
}
