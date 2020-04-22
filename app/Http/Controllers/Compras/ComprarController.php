<?php

namespace App\Http\Controllers\Compras;

use App\Compra;
use App\Concepto;
use App\Deposito;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\StoreComprar;

class ComprarController extends Controller
{
    public function index(){

        if (request()->wantsJson())
        return [
            'conceptos' => Concepto::selConceptosC()->comprar()->get(),
        ];

    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreComprar $request)
    {
        $data = $request->validated();

        $data['empresa_id'] =  session()->get('empresa')->id;
        $data['username'] = $request->user()->username;

        $reg = Deposito::create($data);

        $this->cambiaACompra($reg->compra_id, $data['importe']);

        $compra = Compra::with(['cliente','grupo','tipo','fase'])->findOrFail($reg->compra_id);

        if (request()->wantsJson())
            return [
                'compra'    => $compra,
                'lineas'    => Deposito::CompraId($reg->compra_id)->get(),
                'message'   => 'EL registro ha sido creado'
            ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $deposito = Deposito::findOrFail($id);

        $deposito->delete();

        $this->cambiaAReCompra($deposito->compra_id, $deposito->importe);


        $compra = Compra::with(['cliente','grupo','tipo','fase'])->findOrFail($deposito->compra_id);

        if (request()->wantsJson())
            return [
                'compra' => $compra,
                'lineas' => Deposito::CompraId($deposito->compra_id)->get(),
           ];
    }

    /**
     * @param $id
     * @param $importe
     */

    private function cambiaACompra($id, $importe){

        $compra = Compra::findOrFail($id);

        $data_com['importe'] = $compra->importe + $importe;
        $data_com['fecha_renovacion'] = null;
        $data_com['tipo_id'] = 2;
        $data_com['retencion'] = session()->get('parametros')->retencion;
        $data_com['username'] = session('username');

        $compra->update($data_com);
    }

    /**
     * @param $id
     * @param $importe
     */
    private function cambiaAReCompra($id, $importe){

        $compra = Compra::findOrFail($id);

        $data_com['importe'] = $compra->importe - $importe;
        $data_com['fecha_renovacion'] = $compra->fecha_compra->addDays($compra->dias_custodia);
        $data_com['tipo_id'] = 1;
        $data_com['retencion'] = 0;
        $data_com['username'] = session('username');


        $compra->update($data_com);
    }
}
