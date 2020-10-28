<?php

namespace App;

use App\Scopes\EmpresaScope;
use App\Scopes\EmpresaProductoScope;
use Illuminate\Database\Eloquent\Model;

class Recuento extends Model
{
    protected $fillable = [
        'empresa_id','fecha', 'producto_id', 'estado_id','rfid_producto_id','destino_empresa_id','rfid_id','username'
    ];

    protected static function boot()
    {
        parent::boot();

        //static::addGlobalScope(new EmpresaProductoScope);
        static::addGlobalScope(new EmpresaScope);
    }

    public function producto()
    {
        return ($this->belongsTo(Producto::class));

    }

    public function estado()
    {
    	return ($this->belongsTo(Estado::class));
    }

    public function rfid()
    {
    	return ($this->belongsTo(Rfid::class));
    }

    public static function scopeRfid($query, $rfid_id){

        if ($rfid_id == null)
            return $query;

        return $query->where('rfid_id', '=', $rfid_id);

    }



}
