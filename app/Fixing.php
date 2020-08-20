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
}
