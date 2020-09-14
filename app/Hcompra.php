<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hcompra extends Model
{

    protected $fillable = [
        'empresa_id', 'grupo_id','dias_custodia', 'ejercicio','serie_com','albaran','cliente_id','tipo_id',
        'fecha_compra','fecha_bloqueo','fecha_renovacion','fecha_recogida','importe',
        'importe_renovacion','importe_acuenta', 'interes','fase_id', 'factura','fecha_factura',
        'serie_fac','papeleta','notas', 'username','retencion', 'interes_recuperacion', 'importe_recuperacion','almacen_id',
        'id', 'operacion', 'username_his', 'created_his', 'compra_id'
     ];

}
