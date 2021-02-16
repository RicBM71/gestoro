<?php

namespace App\Http\Controllers\Compras;

use App\Iva;
use App\Libro;
use App\Compra;
use App\Comline;
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
            'conceptos1' => Concepto::selConceptosC()->where('id', 10)->get(),
            'conceptos2' => Concepto::selConceptosC()->whereIn('id',[11,12])->get(),
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

        $importe_total = $data['importe'];
        if ($data['importe1'] > 0){ // hay dos medios de pago en la recuperacion
            $data['importe'] = $data['importe1'];
            // crea el primero
            $reg = Deposito::create($data);
        }

        if ($data['importe2'] > 0){ // hay dos medios de pago en la recuperacion
            // crea el segundo
            $data['importe'] = $data['importe2'];
            $data['concepto_id'] = $data['concepto_id2'];
            $reg = Deposito::create($data);
        }

        $this->actualizaCompra($reg->compra_id, $importe_total, $data['fecha']);


        $compra = Compra::findOrFail($reg->compra_id);

        if (session('parametros')->facturar_al_recuperar){

            $this->facturarCompra($compra, $data['fecha']);
            $compra = Compra::findOrFail($reg->compra_id);
            
        }

        if (request()->wantsJson())
            return [
                'compra'    => $compra->load(['cliente','grupo','tipo','fase']),
                'lineas'    => Deposito::CompraId($reg->compra_id)->get(),
                'message'   => 'EL registro ha sido creado'
            ];
    }

    private function facturarCompra($compra, $fecha){

        $libro = Libro::getContadorCompra(getEjercicio($fecha),$compra->grupo_id);

        $iva = Iva::findOrFail(2); // rebu.

        $data['username'] = session('username');

        $incremento = round($compra->importe_acuenta * 100 / $compra->importe, 2) - 100;
        $comlines = Comline::where('compra_id', $compra->id)->get();

        // recalcula importe venta con importe de recuperación, lo reparte en líneas.
        foreach ($comlines as $comline){
            $pvp = $comline->importe + round($comline->importe * $incremento / 100, 2);
            $data['iva']=$iva->importe;
            $data['importe_venta']=$pvp;

            $comline->update($data);
        }


        $libro->ult_factura = $libro->ult_factura + 1;

        $data_com = [
            'username'     => session('username'),
            'serie_fac'    => $libro->serie_fac,
            'factura'      => $libro->ult_factura,
            'fecha_factura'=> $fecha
        ];

        // actualiza número y fecha factura
        $compra->update($data_com);

        // actualiza el contador de facturas de recuperaciones.
        $libro->update(['factura'=>$libro->ult_factura]);


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

        // por si hay recuperacion con dos medios de pago

        $depositos = Deposito::where('compra_id', $deposito->compra_id)
                              ->whereIn('concepto_id',[10,11,12])
                              ->get();
        foreach ($depositos as $row){

            $this->actualizaCompra($row->compra_id, $row->importe * -1, null);
            $row->delete();

        }


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

        $imp_renovacion = round(($compra->importe - ($compra->importe_acuenta + $importe))  * $compra->interes / 100, 0);
        $imp_recuperacion = round(($compra->importe - ($compra->importe_acuenta + $importe))  * $compra->interes_recuperacion / 100, 0);

        //($imp_renovacion < 0) ?: 0;
        if ($imp_renovacion < 0) $imp_renovacion = 0;
        if ($imp_recuperacion < 0) $imp_recuperacion = 0;

        $fase_id = ($importe > 0) ? 5 : 4;

        $data_com['fase_id'] = $fase_id;

        //if ($compra->fecha_recogida < $fecha)
        if ($compra->fecha_recogida == null)
            $data_com['fecha_recogida']  = $fecha;

        $data_com['importe_acuenta'] = $compra->importe_acuenta + $importe;
        $data_com['importe_renovacion'] = $imp_renovacion;
        $data_com['importe_recuperacion'] = $imp_recuperacion;
        $data_com['username'] = session('username');

        $compra->update($data_com);
    }
}
