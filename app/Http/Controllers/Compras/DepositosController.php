<?php

namespace App\Http\Controllers\Compras;

use App\Banco;
use App\Compra;
use App\Cliente;
use App\Concepto;
use App\Deposito;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\StoreDeposito;

class DepositosController extends Controller
{

  /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

         if (request()->wantsJson())
            return [
                'lineas' => Deposito::CompraId($id)->get(),
              //  'totales' => Comline::totalCompra($id)
            ];
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if (request()->wantsJson())
            return [
                'conceptos' => Concepto::selConceptosC()->depositos()->get(),
                'bancos'    => Banco::selEntidadBic(),
            ];

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDeposito $request)
    {

        $data = $request->validated();

        $compra = Compra::findOrFail($request->compra_id);

        $data['cliente_id'] =  $compra->cliente_id;

        $data['empresa_id'] = session()->get('empresa')->id;
        $data['username'] = $request->user()->username;

        if ($data['concepto_id']==2){
            Cliente::updateIBAN($compra->cliente_id,$data['iban'],$data['bic']);
        }else{
            $data['iban']=null;
            $data['bic']=null;
        }
        // $data['importe'] = $request->importe;
        // $data['concepto_id'] = $request->concepto_id;
        // $data['fecha'] = $request->fecha;


        $reg = Deposito::create($data);

        //$data['importe_renovacion'] = round($compra->importe * $compra->interes / 100, 0);

        if (request()->wantsJson())
            return [
            ];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deposito $deposito)
    {
       // $this->authorize('delete', $deposito);

        if ($deposito->concepto_id <=3){
            $this->updateFase($deposito->compra_id, 1);
        }

        $deposito->delete();

        if (request()->wantsJson())
            return [
                'compra' => Compra::with(['cliente','grupo','tipo','fase'])->findOrFail($deposito->compra_id),
                'lineas' => Deposito::CompraId($deposito->compra_id)->get(),
           ];
    }

    public function updateFase($id,$fase_id){

        $data=[
            'fase_id'=> $fase_id,
            'username' => session()->get('username')
        ];

        Compra::where('id', $id)->update($data);

    }

    /**
     *  Obtenemos compra a partir del depósito
     *
     * @param Deposito $deposito
     * @return void
     */
    public function compra(Deposito $deposito)
    {
        $compra = Compra::findOrFail($deposito->compra_id);

         if (request()->wantsJson())
            return $compra;
    }

}
