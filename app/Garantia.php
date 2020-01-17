<?php

namespace App;

use App\Scopes\EmpresaComunScope;
use Illuminate\Database\Eloquent\Model;

class Garantia extends Model
{

    protected $fillable = [
        'empresa_id','nombre','leyenda', 'username'
    ];


    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaComunScope);

    }

    public static function selGarantias(){

        return Garantia::select('id AS value', 'nombre AS text')
                            ->orderBy('nombre', 'asc')
                            ->get();

    }
}
