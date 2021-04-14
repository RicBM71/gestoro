<?php

namespace App\Http\Controllers\WooCommerce;

use App\Producto;
use Illuminate\Http\Request;
use App\Traits\WooCommerceTrait;
use App\Http\Controllers\Controller;

class WooPedidosController extends Controller
{

    use WooCommerceTrait;

    /**
     * Verifica si hay pedidos pendientes de procesar
     *
     * @return integer
     */
    public function index(){

        $this->test();

        return $this->check();

    }

    /**
     *
     * Crea albaranes a partir de pedidos pendientes de procesar.
     * Reasiga a su empresa correspondiente.
     *
     * @return array // número de albaranes creados por empresa.
     *
     */

    public function pendientes(){

        $pedidos = $this->processing();

        return $pedidos;

    }


    /**
     * Crea productos
     *
     * @return void
     */
    public function producto(Request $request, Producto $producto){

        // $data = $request->validate([
        //     'producto_id' => ['required','integer']
        // ]);


        //return $producto->load('clase','empresa','tags');

        $ec = $this->store_producto($producto);

        $upd['online'] = true;
        $upd['username'] = $request->user()->username;
        $upd['ecommerce_id'] = $ec->id;

        $producto->update($upd);

        if (request()->wantsJson())
            return [
                'producto'=> $producto->load('clase','empresa','tags'),
                'stock_real'=> Producto::getStockReal($producto->id),
            ];

    }

    /**
     * crea categorías
     *
     * @return void
     */
    public function categoria(){

    }
}
