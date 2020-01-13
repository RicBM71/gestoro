<?php

namespace App\Http\Controllers\Utilidades;

use App\Cobro;
use App\Compra;
use App\Albaran;
use App\Deposito;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReasignarClienteController extends Controller
{
    public function reasignar(Request $request){

        $data = $request->validate([
            'id' => ['required','integer'],
            'cliente_id' => ['required','integer'],
            'tipo_op' => ['required']
        ]);

        if ($data['tipo_op'] == "V")
            return $this->venta($data);
        else
            return $this->compra($data);

    }

    private function venta($param){

        $albaran = Albaran::findOrFail($param['id']);

        $data['username']=session('username');
        $data['cliente_id']=$param['cliente_id'];

        $albaran->update($data);

        $data['updated_at']=Carbon::now();

        Cobro::where('albaran_id', $param['id'])->update($data);

        if (request()->wantsJson())
            return [
                'albaran' => $albaran->load(['cliente','tipo','fase','motivo','fpago','cuenta','procedencia']),
                'message' => 'EL cliente ha sido modificado'
                ];

    }

    private function compra($param){

        $compra = Compra::findOrFail($param['id']);

        $data['username']=session('username');
        $data['cliente_id']=$param['cliente_id'];

        $compra->update($data);

        $data['updated_at']=Carbon::now();

        Deposito::where('compra_id', $param['id'])->update($data);

        if (request()->wantsJson())
            return [
                'compra' => $compra->load(['cliente','grupo','tipo','fase']),
                'message' => 'EL cliente ha sido modificado'
                ];

    }

}
