<?php

namespace App\Traits;

use App\Cobro;
use App\Albalin;
use App\Albaran;
use App\Contador;
use App\Producto;
use Carbon\Carbon;
use App\Scopes\EmpresaScope;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Scopes\EmpresaProductoScope;


trait WoocommerceTrait {

    protected $albaranes_creados;
    protected $id_albaranes_creados;

    public function woo_connect()
    {
        if (config('cron.woo_url') == false)
            return false;

        $url = config('cron.woo_url');
        $key = config('cron.woo_key');
        $sec = config('cron.woo_sec');

        $woocommerce = new Client(
            $url,
            $key,
            $sec,
            [
                'wp_api' => true,
                'version' => 'wc/v3'
            ]
        );

        return $woocommerce;

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

        //'stock_quantity'    => 1,
          //  'stock_status'      => 'instock',

        //$data = ($estado_id <= 2) ? ['stock_status' => 'instock'] : ['stock_status' => 'outofstock'];
        $data = ($estado_id <= 2) ? ['stock_quantity' => '1'] : ['stock_quantity' => '0'];

        \Log::info('woo_update_pro: estado: '.$estado_id);

        $woocommerce->put('products/'.$producto_ecommerce_id, $data);

    }


    public function woo_check(){

        $woocommerce = $this->woo_connect();
        if ($woocommerce === false)
            return 0;

        $filter = ['status' => 'processing'];
        $pedidos = $woocommerce->get('orders',$filter);

        return collect($pedidos)->count();

    }

    public function woo_processing(){

        $woocommerce = $this->woo_connect();
        if ($woocommerce === false)
            return 0;

        $filter = ['status' => 'processing'];
        $pedidos = $woocommerce->get('orders', $filter);


        $this->id_albaranes_creados=array();
        $i = 0;
        foreach ($pedidos as $pedido){

            $this->albaranes_creados = 0;

            $lineas = $pedido->line_items;

            foreach ($lineas as $linea){

                //\Log::info($pedido->order_key.' SKU: '.$linea->sku);

                $p = Producto::withoutGlobalScope(EmpresaProductoScope::class)->with(['iva'])->where('referencia', '=', $linea->sku)->get();

                //$p = DB::table('productos')->were

                if ($p->count() == 0){
                    //abort(404, 'No se ha encontrado referencia', $linea->sku);
                    continue;
                }

                $producto = $p->first();

                $cab = [
                    'empresa_id'    => $producto->empresa_id,
                    'fecha_albaran' => substr($pedido->date_created,0,10),
                    'pedido'        => 'W'.$pedido->id, //$pedido->order_key
                    'clitxt'        => $pedido->billing->first_name.' '.$pedido->billing->last_name,
                ];

                $albaran_id = $this->crearAlbaran($cab);

                $this->crearAlbalin($linea, $albaran_id, $producto->empresa_id, $producto);

                $data = ['estado_id' => 4, 'username'=> session('username')];

                $producto->update($data);

                // actualizo WooCommerce
                $this->woo_update_pro($producto->referencia, $producto->ecommerce_id, $data['estado_id']);

            }


            //  Crea cobros
            foreach ($this->id_albaranes_creados as $id){
                $data_cobro = [
                    'albaran_id' => $id,
                    'fpago_id'   => 4,
                ];

                $this->crearCobro($data_cobro);
            }

            $woocommerce->put('orders/'.$pedido->id, ['status'=>'completed']);

            $this->id_albaranes_creados=array();

            $i++;

        }

        return $i;


    }

    private function crearAlbaran($alb){

        $username = session('username') == null ? 'ecommerce' : session('username');

        $a = DB::table('albaranes')
                ->where('empresa_id', '=', $alb['empresa_id'])
                ->where('pedido', $alb['pedido'])
                ->where('fecha_albaran', $alb['fecha_albaran'])
                ->get();

                // el albarán existe y lo retoramos;
        if ($a->count() > 0)
            return $a->first()->id;

        $data_new['empresa_id']     = $alb['empresa_id'];

        $data_new['tipo_id']        = 3;

        $data_new['fecha_albaran']  = $alb['fecha_albaran'];
        $data_new['cliente_id']     = 1;

        $data_new['fase_id']          = 11; // vendido
        $data_new['online']           = true;
        $data_new['validado']         = false;
        $data_new['iva_no_residente'] = false;
        $data_new['username']         = $username;

        $ejercicio   = getEjercicio($alb['fecha_albaran']);
        $contador_alb = Contador::incrementaContadorReubicar($ejercicio, $data_new['tipo_id'], $alb['empresa_id']);
        $data_new['serie_albaran']  = $contador_alb['serie_albaran'];
        $data_new['albaran']        = $contador_alb['ult_albaran'];
        $data_new['pedido']         = $alb['pedido'];
        $data_new['clitxt']         = $alb['clitxt'];
        $data_new['notas_int']      = null;
        $data_new['notas_ext']      = null;
        $data_new['created_at']     = Carbon::now();
        $data_new['updated_at']     = Carbon::now();

        // lo hago así por no andar mareando con estados según voy insertando, será más rápido

        $id = DB::table('albaranes')->insertGetId($data_new);

        $this->albaranes_creados++;
        $this->id_albaranes_creados[] = $id;

        return $id;

    }

    private function crearAlbalin($linea, $albaran_id, $empresa_id, $producto){

        $username = session('username') == null ? 'ecommerce' : session('username');

        // verificamos si ya se ha creado el producto

        $l = Albalin::where('albaran_id', $albaran_id)
                ->where('producto_id', $producto->id)
                ->get()
                ->count();

        if ($l > 0) return; // ya existe

        $albalin_new['albaran_id']      = $albaran_id;
        $albalin_new['empresa_id']      = $empresa_id;
        $albalin_new['producto_id']     = $producto->id;
        $albalin_new['unidades']        = 1;
        $albalin_new['importe_unidad']  = $linea->subtotal;
        $albalin_new['precio_coste']    = $producto->precio_coste;
        $albalin_new['importe_venta']   = $linea->subtotal;
        $albalin_new['iva_id']          = $producto->iva_id;
        $albalin_new['iva']             = $producto->iva->importe;
        $albalin_new['username']        = $username;
        $albalin_new['created_at']      = Carbon::now();
        $albalin_new['updated_at']      = Carbon::now();

        DB::table('albalins')->insert($albalin_new);

    }

    private function crearCobro($data){

        $albaran_new = Albaran::withOutGlobalScope(EmpresaScope::class)->findOrFail($data['albaran_id']);

        $totales = Albalin::totalAlbaranByAlb($albaran_new->id);

        $cobro_new['fecha']      = $albaran_new->fecha_albaran;
        $cobro_new['albaran_id'] = $albaran_new->id;
        $cobro_new['empresa_id'] = $albaran_new->empresa_id;
        $cobro_new['cliente_id'] = $albaran_new->cliente_id;
        $cobro_new['fpago_id']   = $data['fpago_id'];
        $cobro_new['importe']    = $totales['total'];
        $cobro_new['notas']      = 'eCommerce';
        $cobro_new['username']   = $albaran_new->username;
        $cobro_new['created_at'] = Carbon::now();
        $cobro_new['updated_at'] = Carbon::now();

        DB::table('cobros')->insert($cobro_new);

    }


    /**
     * Crea producto WooCommerce
     *
     * @param Object $woocommerce
     * @param Object $producto
     * @return integer ecommerce_id
     */
    public function woo_store_producto($producto){

        $woocommerce = $this->woo_connect();
        if ($woocommerce === false)
            return 0;

            // hay que crear campo de cruce
        //$producto->load('categoria');

        $categoria = [
            [
                'id' => $producto->categoria_id
            ]];

        $data = [
            'name'              => $producto->nombre,
            'type'              => 'simple',
            'sku'               => $producto->referencia,
            'regular_price'     => $producto->precio_ecommerce,
            'description'       => $producto->descripcion,
            'short_description' => $producto->nombre,
            'manage_stock'      => true,
            'stock_quantity'    => 1,
            'stock_status'      => 'instock',
        //    'categories'        => $categoria,
            'sold_individually' => true
        ];

        $prod = $woocommerce->post('products', $data);

        return $prod->id;


    }

    public function woo_test(){

      //  return $this->woo_check();

        $woocommerce = $this->woo_connect();

        if ($woocommerce === false)
            return 'No hay conexión';

       // dd($woocommerce->get('system_status'));

        $data = ['id' => 'bacs'];
        $p = $woocommerce->get('payment_gateways',$data);
        dd($p);


        // $data = ['sku' => 'CL63113'];
        // $p = collect($woocommerce->get('products',$data))->first();

        $p = collect($woocommerce->get('products'));
        dd($p);

        $filter = ['status' => 'processing'];
        $pedidos = $woocommerce->get('orders',$filter);

        dd($pedidos);
        foreach($pedidos as $pedido){

            $cliente = $pedido->billing;

            $lineas = $pedido->line_items;

        }



    }


}
