<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hdeposito extends Model
{
    protected $fillable = [
        'fecha','compra_id','empresa_id', 'cliente_id','dias','concepto_id','importe','dias','notas',
        'iban','bic','username','deposito_id','operacion','created_his','username_his'
    ];

    public function concepto()
    {
    	return $this->belongsTo(Concepto::class);
    }
}
