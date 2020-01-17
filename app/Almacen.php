<?php

namespace App;

use App\Scopes\EmpresaComunScope;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $fillable = [
        'empresa_id','nombre', 'username'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaComunScope);

    }


    public static function selAlmacenes(){

        return Almacen::select('id AS value', 'nombre AS text')
            ->orderBy('nombre', 'asc')
            ->get();

    }


}
