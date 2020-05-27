<?php

namespace App\Http\Controllers\Compras;

use App\Compra;
use App\Comline;
use App\Concepto;
use App\Deposito;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\StoreAcuenta;
use App\Http\Requests\Compras\StoreCapital;

class AmpliarCapitalController extends Controller
{
    public function index(){

        if (request()->wantsJson())
        return [
            'conceptos' => Concepto::selConceptosC()->capital()->get(),
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
    public function store(StoreCapital $request)
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

        $comline = Comline::where('compra_id', $id)->orderby('id')->firstOrFail(); // actualizo sobre la primera

        //$data_com['importe_acuenta'] = $compra->importe_acuenta - $importe;
        $data_com['importe'] = $compra->importe + $importe;
        $data_com['importe_renovacion'] = round(($compra->importe - ($compra->importe_acuenta - $importe))  * $compra->interes / 100, 0);
        $data_com['importe_recuperacion'] = round(($compra->importe - ($compra->importe_acuenta - $importe))  * $compra->interes_recuperacion / 100, 0);
        $data_com['username'] = session('username');

        $imp_new = $comline->importe + $importe;
        $data_lin = ['importe'=> $imp_new, 'username'=> session('username')];

        if ($comline->peso_gr > 0)
            $data_lin['importe_gr'] = setImporteGr($comline->peso_gr, $imp_new);
        else
            $data_lin['importe_gr'] = 0;


        $comline->update($data_lin);

        $compra->update($data_com);



    }

}
