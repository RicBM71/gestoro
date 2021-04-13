<?php

namespace App\Http\Controllers\WooCommerce;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\WooCommerceTrait;

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
    public function producto($producto_id){

        dd($this->store_producto($producto_id));

    }

    /**
     * crea categorías
     *
     * @return void
     */
    public function categoria(){

    }
}
