<?php

namespace App\Http\Controllers\WooCommerce;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\CheckWooCommerceTrait;

class PedidosController extends Controller
{

    use CheckWooCommerceTrait;

    public function index(){

        $this->test();

        return $this->check();

    }

    public function pendientes(){

        $pedidos = $his->processing();

        foreach ($pedidos as $pedido){

        }

    }
}
