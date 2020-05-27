<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $fillable = [
        'lim_efe','lim_efe_nores', 'pie_rebu1','retencion','online',
        'img1','img2', 'carpeta_docs', 'username','aislar_empresas',
        'email_productos_online', 'frm_compras','doble_interes','tag_renovar'
    ];

    public function setFrmComprasAttribute($frm_compras)
    {
        $this->attributes['frm_compras'] = strtoupper($frm_compras);

    }
}
