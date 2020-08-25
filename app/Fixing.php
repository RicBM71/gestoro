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

    public static function getFixDia($clase_id, $fecha){



        if ($fecha == null) return 0;


        $data = Fixing::where('clase_id',$clase_id)
                      ->where('fecha', '<=', $fecha)
                      ->orderBy('fecha', 'desc')
                      ->first();

        return ($data == false) ? 0 : $data->importe;

    }
}
