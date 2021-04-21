<?php

namespace App\Traits;

use Automattic\WooCommerce\Client;


trait WooConnectTrait {


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

        $data = ($estado_id <= 2) ? ['stock_status' => 'instock'] : ['stock_status' => 'outofstock'];

        $woocommerce->put('products/'.$producto_ecommerce_id, $data);


    }


}
