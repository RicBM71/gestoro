<?php

namespace App;

use App\Scopes\EmpresaScope;
use Illuminate\Database\Eloquent\Model;

class Existencia extends Model
{

    protected $dates = ['fecha'];

    protected $fillable = [
        'empresa_id', 'detalle_id', 'fecha','importe', 'username'
    ];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaScope);

    }


}
