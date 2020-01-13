<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Traspaso extends Model
{
    protected $dates =['fecha'];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
    ];


    protected $fillable = [
        'empresa_id', 'fecha','proveedora_empresa_id','fecha','situacion_id','importe_solicitado',
        'importe_entregado','solicitante_user_id','autorizado_user_id','receptor_user_id', 'username',
    ];

    public function solicitante()
    {
    	return $this->belongsTo(User::class,'solicitante_user_id');
    }
    public function autorizado()
    {
    	return $this->belongsTo(User::class,'autorizado_user_id');
    }

    public function receptor()
    {
    	return $this->belongsTo(User::class,'receptor_user_id');
    }

    public function empresa()
    {
    	return $this->belongsTo(Empresa::class);
    }

    public function proveedora()
    {
    	return $this->belongsTo(Empresa::class,'proveedora_empresa_id');
    }
}
