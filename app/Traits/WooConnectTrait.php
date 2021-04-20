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
}
