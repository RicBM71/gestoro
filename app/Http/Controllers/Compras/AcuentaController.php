<?php

namespace App\Http\Controllers\Compras;

use App\Compra;
use App\Concepto;
use App\Deposito;
use App\Http\Requests\Compras\StoreAcuenta;
use App\Http\Controllers\Controller;

class AcuentaController extends Controller
{
    public function index(){

        if (request()->wantsJson())
        return [
            'conceptos' => Concepto::selConceptosC()->acuenta()->get(),
        ];

    }

    public function show($compra_id){

        if (request()->wantsJson())
        return [
            'compra' => Compra::with(['cliente','grupo','tipo','fase'])->findOrFail($compra_id)
        ];

    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAcuenta $request)
    {
        $data = $request->validated();

        $data['empresa_id'] =  session()->get('empresa')->id;
        $data['username'] = $request->user()->username;

        $reg = Deposito::create($data);

        $this->actualizaCompra($reg->compra_id, $data['importe']);

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

        $this->actualizaCompra($deposito->compra_id, $deposito->importe * -1);


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

    private function actualizaCompra($id, $importe){

        $compra = Compra::findOrFail($id);

        $data_com['importe_acuenta'] = $compra->importe_acuenta + $importe;
        $data_com['importe_renovacion'] = round(($compra->importe - ($compra->importe_acuenta + $importe))  * $compra->interes / 100, 0);
        if (session('empresa')->getFlag(10) == false){
            $data_com['importe_recuperacion'] = round(($compra->importe - ($compra->importe_acuenta + $importe))  * $compra->interes_recuperacion / 100, 0);
        }
        $data_com['username'] = session('username');

        $compra->update($data_com);
    }

}
