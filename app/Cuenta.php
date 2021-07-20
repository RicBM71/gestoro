<?php

namespace App;

use App\Scopes\EmpresaScope;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    protected $fillable = [
        'empresa_id','nombre','defecto','activa','iban','bic','username','sufijo_adeudo','sufijo_transf'
    ];

     /**
     *
     * Añadimos global scope para filtrado por empresa.
     *
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaScope);

    }

    public function setNombreAttribute($iban)
    {
        $this->attributes['nombre'] = strtoupper($iban);

    }


    public function setIbanAttribute($iban)
    {
        $this->attributes['iban'] = strtoupper($iban);

    }

    public function setBicAttribute($bic)
    {
        $this->attributes['bic'] = strtoupper($bic);

    }

     /**
     *
     * @return Array formateado para select Vuetify
     *
     */
    public static function selCuentas($activa=null)
    {

        return Cuenta::select('id AS value', 'nombre AS text', 'defecto')
                ->activa($activa)
                ->orderBy('nombre', 'asc')
                ->get();

    }

    public function scopeActiva($query, $activa){

        if (!Empty($activa))
            $query->where('activa', $activa);

        return $query;

    }

    public function scopeDefecto($query){

        $query->where('defecto', true)
              ->where('activa', true);

        return $query;

    }

}
