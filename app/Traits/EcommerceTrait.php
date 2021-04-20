<?php

namespace App\Traits;

use App\Cobro;
use App\Albalin;
use App\Albaran;
use App\Contador;
use App\Producto;
use Carbon\Carbon;
use App\Scopes\EmpresaScope;
use App\Traits\WooConnectTrait;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Scopes\EmpresaProductoScope;


trait EcommerceTrait {

    use WooConnectTrait;

    protected $albaranes_creados;
    protected $id_albaranes_creados;


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

            }


            //  Crea cobros
            foreach ($this->id_albaranes_creados as $id){
                $data_cobro = [
                    'albaran_id' => $id,
                    'fpago_id'   => 2,
                ];

                $this->crearCobro($data_cobro);
            }

            $this->id_albaranes_creados=array();

            $i++;

        }

        print_r($i);


    }

    private function crearAlbaran($alb){

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

        $data_new['fase_id']          = 10; // reservado
        $data_new['online']           = true;
        $data_new['iva_no_residente'] = false;
        $data_new['username']         = session('username');

        $ejercicio   = getEjercicio($alb['fecha_albaran']);
        $contador_alb = Contador::incrementaContadorReubicar($ejercicio, $data_new['tipo_id'], $alb['empresa_id']);
        $data_new['serie_albaran']  = $contador_alb['serie_albaran'];
        $data_new['albaran']        = $contador_alb['ult_albaran'];
        $data_new['pedido']         = $alb['pedido'];
        $data_new['validado']       = false;
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
        $albalin_new['username']        = session('username');
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

        $data = [
            'name'              => $producto->nombre,
            'type'              => 'simple',
            'sku'               => $producto->referencia,
            'regular_price'     => $producto->precio_venta,
            'description'       => $producto->caracteristicas,
            'short_description' => $producto->nombre,
        ];

        $prod = $woocommerce->post('products', $data);

        return $prod->id;


    }

    public function woo_test(){

        $woocommerce = $this->woo_connect();

        if ($woocommerce === false)
            return 'No hay conexión';

        // $data = ['sku' => 'CL63113'];
        // $p = collect($woocommerce->get('products',$data))->first();

        // dd($p);
        $filter = ['status' => 'processing'];
        $pedidos = $woocommerce->get('orders',$filter);

        dd($pedidos);
        foreach($pedidos as $pedido){

            $cliente = $pedido->billing;
            \Log::info(toArray($cliente));

            $lineas = $pedido->line_items;

        }



    }


}
