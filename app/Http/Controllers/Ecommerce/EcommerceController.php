<?php

namespace App\Http\Controllers\Ecommerce;

use App\Producto;
use Illuminate\Http\Request;
use App\Traits\EcommerceTrait;
use Automattic\WooCommerce\Client;
use App\Http\Controllers\Controller;

class EcommerceController extends Controller
{

    use EcommerceTrait;

    protected $woocommerce;
    protected $ecommerce="woo";

    public function __construct()
    {

        $url = config('cron.woo_url');

        if ($url == false){
            abort(404, 'No hay tienda online configurada!');
        }

        $key = config('cron.woo_key');
        $sec = config('cron.woo_sec');

        $this->woocommerce = new Client(
            $url,
            $key,
            $sec,
            [
                'wp_api' => true,
                'version' => 'wc/v3'
            ]
        );
    }
    /**
     * Verifica si hay pedidos pendientes de procesar
     *
     * @return integer
     */
    public function index(){

        $this->test($this->woocommerce);

        return $this->check($this->woocommerce);

    }

    /**
     *
     * Crea albaranes a partir de pedidos pendientes de procesar.
     * Reasiga a su empresa correspondiente.
     *
     * @return array // número de albaranes creados por empresa.
     *
     */

    public function processing(){

        if ($this->ecommerce == 'woo')
            $pedidos = $this->woo_processing($this->woocommerce);

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

        if ($this->ecommerce == 'woo')
            $ecommerce_id = $this->woo_store_producto($this->woocommerce, $producto);

        $upd['online'] = true;
        $upd['username'] = $request->user()->username;
        $upd['ecommerce_id'] = $ecommerce_id;

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
