<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quilate extends Model
{
    public static function selQuilates()
    {
        return Quilate::select('id AS value', 'nombre AS text')
            ->orderBy('id', 'asc')
            ->get();

    }
}
