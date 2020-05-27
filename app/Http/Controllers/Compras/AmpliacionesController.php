<?php

namespace App\Http\Controllers\Compras;

use App\Compra;
use App\Concepto;
use App\Deposito;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\StoreAmpliacion;

class AmpliacionesController extends Controller
{

    public function index(){

        if (request()->wantsJson())
        return [
            'conceptos' => Concepto::selConceptosC()->ampliaciones()->get(),
        ];

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($compra_id)
    {

        $compra = Compra::with(['cliente','grupo','tipo','fase'])->findOrFail($compra_id);

        if ($compra->retraso > 0){
            $retraso = $this->calcularRetraso($compra);
        }else{
            $retraso=[
                'importe'   => $compra->importe_renovacion,
                'dias'      => $compra->dias_custodia
            ];
        }

        if (request()->wantsJson())
            return [
                'ampliacion' => $retraso,
                'com'=> $compra
            ];

    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAmpliacion $request)
    {

        $data = $request->validated();

        $compra = Compra::findOrFail($data['compra_id']);


        // if ($compra->retraso > $compra->libro->dias_cortesia && $data['dias'] < $compra->dias_custodia)
        //     return abort(422, 'Dias fuera de rango mínimo '.$compra->dias_custodia);

        $data['empresa_id'] =  session()->get('empresa')->id;
        $data['username'] = $request->user()->username;

        // $retraso = $this->calcularRetraso($compra);
        // if ($retraso['dias'] <> $data['dias'] || $retraso['importe'] <> $data['importe']){
        //     $valor_teo = round($data['dias'] * $retraso['precio_dia'], 0);
        //     $notas = '# Val. Teo: '.getCurrency($valor_teo).'. Retraso: '.$retraso['retraso_real'].' días. Importe:'. getCurrency($retraso['importe']).' ';
        //  //    falla si notas es nulo, de momento lo dejo, no lo veo ahora muy interesante el controlar el rectificado, ya veremos.
        //     (isset($data['notas'])) ?  $data['notas'].=$notas : $data['notas'] = $notas;
        // }


        $reg = Deposito::create($data);

        $compra = $this->sumaFechaAmpliacionCompra($reg->compra_id, $data['dias']);

        if (request()->wantsJson())
            return [
                'compra'    => $compra,
                'lineas'    => Deposito::CompraId($reg->compra_id)->get(),
                'message'   => 'EL registro ha sido creado'
            ];
    }

    /**
     * @param $compra_id integer
     * @param $dias     integer
     * @return $compra Object
     */
    private function sumaFechaAmpliacionCompra($compra_id, $dias){


        $compra = Compra::with(['cliente','grupo','tipo','fase'])->findOrFail($compra_id);

        $fecha = Carbon::parse($compra->fecha_renovacion);

        $data['fecha_renovacion'] = $fecha->addDays($dias);
        $data['username'] = session('username');

        $compra->update($data);

        return $compra;

    }

    /**
     * Calcula el retraso
     * @compra Object
     * @return array
     *
     */
    private function calcularRetraso($compra){

        $retraso = $compra->retraso;

        // if ($retraso < 30)
        //     $retraso = 30;

        $cuotas = round($retraso / $compra->dias_custodia,2);

        $importe = round($compra->importe_renovacion * $cuotas, 0);

        return [
            'importe'      => $importe,
            'retraso_real' => $compra->retraso,
            'dias'         => $retraso,
            'precio_dia'   => round($compra->importe_renovacion / $compra->dias_custodia, 2)
        ];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deposito $ampliacione)
    {

        $ampliacione->delete();

        $compra = $this->restaFechaAmpliacionCompra($ampliacione->compra_id, $ampliacione->dias);

        if (request()->wantsJson())
            return [
                'compra' => $compra,
                'lineas' => Deposito::CompraId($ampliacione->compra_id)->get(),
           ];
    }

     /**
     * @param $compra_id integer
     * @param $dias     integer
     * @return $compra Object
     */
    private function restaFechaAmpliacionCompra($compra_id, $dias){

        $compra = Compra::with(['cliente','grupo','tipo','fase'])->findOrFail($compra_id);

        $fecha = Carbon::parse($compra->fecha_renovacion);

        $data['fecha_renovacion'] = $fecha->subDays($dias);
        $data['username'] = session('username');

        $compra->update($data);

        return $compra;

    }


}
