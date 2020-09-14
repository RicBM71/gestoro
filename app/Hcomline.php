<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hcomline extends Model
{
    protected $fillable = [
        'comline_id','empresa_id','compra_id','concepto','grabaciones','colores','clase_id','peso_gr','importe',
        'importe_venta','iva',
        'importe_gr','quilates','fecha_liquidado','username','operacion','created_his','username_his'
    ];

    public function clase()
    {
    	return $this->belongsTo(Clase::class);
    }

}
