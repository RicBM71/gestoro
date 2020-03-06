<?php

namespace App\Http\Controllers\Utilidades;

use App\Deposito;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpDepositosController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'compra_id'=> ['required','integer'],
            'cliente_id'=> ['required','integer']
        ]);

        if (request()->wantsJson())
            return [
                'totales_concepto' => Deposito::totalesConcepto($data['compra_id']),
                'valor_entregado'   => Deposito::valorEntregado(date('Y-m-d'), $data['cliente_id']),
                'valor_recibido'   => Deposito::valorRecibido(date('Y-m-d'), $data['cliente_id'])
            ];

    }
}
