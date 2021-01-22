<?php

namespace App;

use App\Scopes\EmpresaScope;
use Illuminate\Database\Eloquent\Model;

class Whatsapp extends Model
{
    protected $fillable = [
        'empresa_id', 'texto', 'username'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaScope);
    }
}
