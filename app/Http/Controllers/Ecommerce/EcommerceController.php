<?php

namespace App\Http\Controllers\Ecommerce;

use App\Albaran;
use App\Producto;
use App\Scopes\EmpresaScope;
use Illuminate\Http\Request;
use App\Traits\WoocommerceTrait;
use Automattic\WooCommerce\Client;
use App\Http\Controllers\Controller;

class EcommerceController extends Controller
{

    use WoocommerceTrait;

    protected $woocommerce=false;
    protected $ecommerce="woo";

    public function __construct()
    {

        $url = config('cron.woo_url');

        if ($url == false){
            abort(404, 'No hay tienda online configurada!');
        }
    }


    /**
     * Verifica si hay pedidos pendientes de procesar
     *
     * @return integer
     */
    public function index(){

        $data = Albaran::withOutGlobalScope(EmpresaScope::class)->with(['empresa','cliente'])
                        ->where('validado',false)
                        ->get();

        if (request()->wantsJson())
            return $data;

        // if ($this->ecommerce == 'woo')
        //     return $this->woo_test();


    }

    public function validar(Request $request, Albaran $albaran){

        if (!hasECommerce())
             abort(403, 'Permiso eCommerce requerido!');

        $data = $request->validate([
            'validado'      => ['boolean', 'required'],
        ]);

        $data['username'] = $request->user()->username;
        $data['validado'] = !$data['validado'];

        $albaran->update($data);

        if (request()->wantsJson())
            return [
                'message' => 'Pedido procedente eCommerce Validado!'
            ];

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
            $pedidos = $this->woo_processing();

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
            $ecommerce_id = $this->woo_store_producto($producto);

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
