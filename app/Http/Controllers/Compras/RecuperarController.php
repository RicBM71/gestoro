<?php

namespace App\Http\Controllers\Compras;

use App\Compra;
use App\Concepto;
use App\Deposito;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\StoreRecuperar;

class RecuperarController extends Controller
{
    public function index(){

        if (request()->wantsJson())
        return [
            'conceptos' => Concepto::selConceptosC()->recuperar()->get(),
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
    public function store(StoreRecuperar $request)
    {
        $data = $request->validated();

        $data['empresa_id'] = session()->get('empresa')->id;
        $data['username'] = $request->user()->username;

        $reg = Deposito::create($data);

        $this->actualizaCompra($reg->compra_id, $data['importe'], $data['fecha']);

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

        $this->actualizaCompra($deposito->compra_id, $deposito->importe * -1, null);


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

    private function actualizaCompra($id, $importe, $fecha=false){

        //TODO: Más adelante quizás interese actualizar fecha de recogida que pasaría a ser
        //      la fecha de recuperación con el fin de modificar el recálculo de facturas
        //      de recuperaciones y partir de compra en vez de depósito para el rango de fechas.

        $compra = Compra::findOrFail($id);

        $imp_ren = round(($compra->importe - ($compra->importe_acuenta + $importe))  * $compra->interes / 100, 0);

        //($imp_ren < 0) ?: 0;
        if ($imp_ren < 0) $imp_ren = 0;

        $fase_id = ($importe > 0) ? 5 : 4;

        $data_com['fase_id'] = $fase_id;

        if ($compra->fecha_recogida < $fecha)
            $data_com['fecha_recogida']  = $fecha;
        $data_com['importe_acuenta'] = $compra->importe_acuenta + $importe;
        $data_com['importe_renovacion'] = $imp_ren;
        $data_com['username'] = session('username');

        $compra->update($data_com);
    }
}
