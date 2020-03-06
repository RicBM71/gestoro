<?php

namespace App;

use App\Scopes\EmpresaScope;
use Illuminate\Database\Eloquent\Model;

class Cruce extends Model
{

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaScope);

    }

    protected $fillable = [
        'empresa_id', 'comven', 'destino_empresa_id', 'username'
    ];

    public function scopeCompra($query)
    {
        return $query->where('comven', 'C' );

    }

    public function scopeVenta($query)
    {
        return $query->where('comven', 'V' );

    }

    public function destino()
    {
    	return ($this->belongsTo(Empresa::class, 'destino_empresa_id'));
    }


}
