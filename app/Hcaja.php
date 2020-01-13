<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hcaja extends Model
{
    protected $fillable = [
        'empresa_id', 'caja_id', 'fecha', 'dh', 'nombre', 'importe', 'manual','deposito_id', 'cobro_id',
        'username','operacion','created_at','updated_at','created_his','username_his'
    ];
}
