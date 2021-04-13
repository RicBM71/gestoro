<?php

namespace App\Traits;

use App\Producto;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\DB;


trait CheckWooCommerceTrait {

    protected $woocommerce;

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

    public function check(){

        $filter = ['status' => 'processing'];
        $pedidos = $this->woocommerce->get('orders',$filter);

        return collect($pedidos)->count();

    }

    public function processing(){

        $filter = ['status' => 'processing'];
        $pedidos = $this->woocommerce->get('orders',$filter);


        $albaran = array();

        foreach ($pedidos as $pedido){

            $lineas = $pedido->line_items;
            foreach ($lineas as $linea){

                \Log::info($pedido->order_key.' SKU: '.$linea->sku);

                //$data = ['estado_id' => 1, 'username'=> session('username')];

                //Producto::where('referencia', $linea->sku)->update($data);
            }

        }

    }


    public function test(){

        $data = ['sku' => 'logo-collection'];
        $p = collect($this->woocommerce->get('products',$data))->first();

       // dd($p);
        $filter = ['status' => 'processing'];
        $pedidos = $this->woocommerce->get('orders',$filter);

        dd($pedidos);
        foreach($pedidos as $pedido){

            $cliente = $pedido->billing;
            \Log::info(toArray($cliente));

            $lineas = $pedido->line_items;

        }



    }


}
