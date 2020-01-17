<?php

namespace App;

use App\Scopes\EmpresaComunScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taller extends Model
{
    use SoftDeletes;

    protected $table = 'talleres';

    protected $fillable = [
        'empresa_id','nombre', 'razon', 'apellidos', 'direccion','cpostal','poblacion', 'provincia', 'telefono1', 'telefono2',
        'tfmovil','email', 'tipodoc', 'dni', 'notas', 'facturar', 'fpago_id','iban','bic','username'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaComunScope);

    }

    public function setDniAttribute($dni)
    {
        $this->attributes['dni'] = strtoupper($dni);

    }

    public static function selTalleres()
    {

        return Taller::select('id AS value', 'razon AS text')
            ->orderBy('nombre', 'asc')
            ->get();

    }
}
