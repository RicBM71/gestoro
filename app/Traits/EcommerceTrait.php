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


trait EcommerceTrait {

    protected $woocommerce;
    protected $albaranes_creados;
    protected $albaranes_empresa;

    public function __construct()
    {

        $url = config('cron.woo_url');
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

    public function check($woocommerce){

        $filter = ['status' => 'processing'];
        $pedidos = $woocommerce->get('orders',$filter);

        return collect($pedidos)->count();

    }

    public function processing($woocommerce){

        $filter = ['status' => 'processing'];
        $pedidos = $woocommerce->get('orders',$filter);

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

                $albaran_id = $this->crearAlbaran($producto->empresa_id, $pedido);

                $this->crearAlbalin($linea, $albaran_id, $producto->empresa_id, $producto);

                $data = ['estado_id' => 4, 'username'=> session('username')];
                $producto->update($data);

            }

            //aquí crear cobro. Ver var albaranes_creados para crear 1 mov. de cobro o más.

            $i++;

        }

        print_r($i);
        dd($this->albaranes_empresa);

    }

    private function crearAlbaran($empresa_id, $pedido){

        $fecha_albaran = substr($pedido->date_created,0,10);

        $pedido_ref = 'W'.$pedido->id;//$pedido->order_key

        $a = DB::table('albaranes')
                ->where('empresa_id', '=', $empresa_id)
                ->where('pedido', $pedido_ref)
                ->where('fecha_albaran', $fecha_albaran)
                ->get();

                // el albarán existe y lo retoramos;
        if ($a->count() > 0)
            return $a->first()->id;

        $data_new['empresa_id']     = $empresa_id;

        $data_new['tipo_id']        = 3;

        $data_new['fecha_albaran']  = $fecha_albaran;
        $data_new['cliente_id']     = 1;

        $data_new['fase_id']          = 11; // vendido
        $data_new['online']           = true;
        $data_new['iva_no_residente'] = false;
        $data_new['username']         = session('username');

        $ejercicio   = getEjercicio($fecha_albaran);
        $contador_alb = Contador::incrementaContadorReubicar($ejercicio, $data_new['tipo_id'], $empresa_id);
        $data_new['serie_albaran']    = $contador_alb['serie_albaran'];
        $data_new['albaran']      = $contador_alb['ult_albaran'];
        $data_new['pedido']       = $pedido_ref;
        $data_new['clitxt']       = $pedido->billing->first_name.' '.$pedido->billing->last_name;
        $data_new['notas_int']    = null;
        $data_new['notas_ext']    = null;
        $data_new['created_at']   = Carbon::now();
        $data_new['updated_at']   = Carbon::now();

        // lo hago así por no andar mareando con estados según voy insertando, será más rápido

        $id = DB::table('albaranes')->insertGetId($data_new);

        $this->albaranes_creados++;

        if (!isset($this->albaranes_empresa[$empresa_id]))
            $this->albaranes_empresa[$empresa_id]=0;

        $this->albaranes_empresa[$empresa_id]++;

        return $id;

        //$this->contador++;

        //return Albaran::withoutGlobalScope(EmpresaScope::class)->find($id);
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

    private function crearCobro($albaran_new, $albaran_id_ant, $total_albaran){

        $cobros = Cobro::where('albaran_id',$albaran_id_ant)
                    ->orderBy('fecha','asc')
                    ->get();

        $sw=false;
        foreach ($cobros as $cobro){
            $sw=true;
            if ($cobro->fpago_id == 1)
                DB::table('cajas')
                    ->where('cobro_id', $cobro->id)
                    ->update(['cobro_id'  => null,
                              'manual'    => 'S',
                            //   'username'  => session('username'),
                            //   'updated_at'=>Carbon::now()
                            ]);

        }

        if ($sw===false)
            return;

        $cobro_new['fecha']      = $cobro->fecha;
        $cobro_new['albaran_id'] = $albaran_new->id;
        $cobro_new['empresa_id'] = $albaran_new->empresa_id;
        $cobro_new['cliente_id'] = $albaran_new->cliente_id;
        $cobro_new['fpago_id']   = 2;
        $cobro_new['importe']    = $total_albaran;
        $cobro_new['notas']      = 'x REUBICACIÓN';
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
    public function woo_store_producto($woocommerce, $producto){

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


    public function test($woocommerce){

        $data = ['sku' => 'CL63113'];
        $p = collect($woocommerce->get('products',$data))->first();

        dd($p);
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
