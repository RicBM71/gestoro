<?php

namespace App;

use App\Scopes\EmpresaScope;
use Illuminate\Database\Eloquent\Model;

class Socialmedia extends Model
{
    protected $fillable = [
        'empresa_id', 'texto', 'logo','username'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaScope);
    }

}
