<?php

namespace App\Traits;

use App\Producto;
use App\Traits\WooConnectTrait;
use Illuminate\Support\Facades\DB;
use App\Scopes\EmpresaProductoScope;


trait EstadoProductoTrait {

    use WooConnectTrait;

    public function setEstadoProducto($producto_id, $estado_id){


        $producto = Producto::withoutGlobalScope(EmpresaProductoScope::class)->findOrFail($producto_id);


        $data=[
            'estado_id'=> $estado_id,
            'username' => session('username')
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
            $this->woo_update_pro($producto->referencia, $producto->ecommerce_id, $estado_id);
        }

    }

    /**
     *
     * Actualiza estado producto para WooCommerce.
     * @
     *
     */

    private function woo_update_pro($referencia, $producto_ecommerce_id, $estado_id){

        $woocommerce = $this->woo_connect();

        // si tengo el ID de Woo no lo busco
        if ($producto_ecommerce_id == null){
            $data = ['sku' => $referencia];
            $woo_producto = collect($woocommerce->get('products',$data))->first();

            $producto_ecommerce_id = $woo_producto->id;

        }

        $data = ($estado_id <= 2) ? ['stock_status' => 'instock'] : ['stock_status' => 'outofstock'];

        $woocommerce->put('products/'.$producto_ecommerce_id, $data);


    }


}
