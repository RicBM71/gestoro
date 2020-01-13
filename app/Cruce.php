<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cruce extends Model
{

    public function scopeCompra($query)
    {
        $query->where('comven', 'C' );

    }

    public function scopeVenta($query)
    {
        $query->where('comven', 'V' );

    }

}
