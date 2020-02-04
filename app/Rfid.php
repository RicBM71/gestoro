<?php

namespace App;

use App\Rfid;
use Illuminate\Database\Eloquent\Model;

class Rfid extends Model
{
    public static function selRfid()
    {

        return Rfid::select('id AS value', 'nombre AS text')
            ->orderBy('nombre', 'asc')
            ->get();

    }
}
