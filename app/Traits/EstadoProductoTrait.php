<?php

namespace App\Traits;

use App\Producto;
use App\Jobs\WooUpdateProJob;
use App\Traits\WoocommerceTrait;
use Illuminate\Support\Facades\DB;
use App\Scopes\EmpresaProductoScope;


trait EstadoProductoTrait {

    use WoocommerceTrait;

    public function setEstadoProducto($producto_id, $estado_id){

        $username = session('username') == null ? 'ecommerce' : session('username');

        $producto = Producto::withoutGlobalScope(EmpresaProductoScope::class)->findOrFail($producto_id);


        $data=[
            'estado_id'=> $estado_id,
            'username' => $username
        ];


        if ($producto->estado_id >= 5) return;

        if ($producto->stock > 1){

            $vendidos = DB::table('albalins')
                            ->where('producto_id', $producto_id)
                            ->whereNull('deleted_at')
                            ->sum('unidades');

            if ($vendidos >= $producto->stock){
                if ($estado_id == 4)        // no actualizamos para que no se quede en reservado, así no se podría volver a vender.
                    $producto->update($data);
            }else if ($estado_id == 2) {
                $producto->update($data);    // actualizamos para dejarlo en venta.
            }

        }else{

            $producto->update($data);
        }

        if (config('cron.woo_url') != false && $producto->online == true){

            dispatch(new WooUpdateProJob($producto->referencia, $producto->ecommerce_id, $estado_id));
            //$this->woo_update_pro($producto->referencia, $producto->ecommerce_id, $estado_id);
        }

    }


}
