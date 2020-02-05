<?php

namespace App;

use App\Scopes\EmpresaProductoScope;
use Illuminate\Database\Eloquent\Model;

class Recuento extends Model
{
    protected $fillable = [
        'empresa_id','fecha', 'producto_id', 'estado_id','producto_id_rfid','destino_empresa_id','rfid_id','username'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaProductoScope);
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




}
