<?php

namespace App\Http\Controllers\Ventas;

use App\Cobro;
use App\Fpago;
use App\Albaran;
use App\Scopes\EmpresaScope;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ventas\StoreCobroRequest;

class CobrosController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($albaran_id)
    {

         if (request()->wantsJson())
            return [
                'lineas'  => Cobro::AlbaranId($albaran_id)->get(),
                'acuenta' => Cobro::getAcuentaByAlbaran($albaran_id),
                'albaran' => Albaran::withOutGlobalScope(EmpresaScope::class)->with(['cliente','tipo','fase','motivo','fpago','cuenta','procedencia'])->findOrFail($albaran_id),
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
                'fpagos' => Fpago::selFPagos()  ,
            ];

    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCobroRequest $request)
    {

        $data = $request->validated();


        // $data['cliente_id'] =  $compra->cliente_id;

        $data['empresa_id'] = session()->get('empresa')->id;
        $data['username'] = $request->user()->username;


        // $data['importe'] = $request->importe;
        // $data['fecha'] = $request->fecha;


        $reg = Cobro::create($data);

        if (request()->wantsJson())
            return [
                'acuenta' => Cobro::getAcuentaByAlbaran($data['albaran_id'])
            ];

    }

      /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cobro $cobro)
    {

        $cobro->forceDelete();

        if (request()->wantsJson())
            return [
                'albaran' => Albaran::with(['cliente','tipo','fase','motivo','fpago','cuenta','procedencia'])->findOrFail($cobro->albaran_id),
                'acuenta' => Cobro::getAcuentaByAlbaran($cobro->albaran_id),
                'lineas'  => Cobro::AlbaranId($cobro->albaran_id)->get(),
           ];
    }

     /**
     *  Obtenemos albarán a partir del cobro
     *
     * @param Cobro $cobro
     * @return void
     */
    public function albaran(Cobro $cobro)
    {

         if (request()->wantsJson())
            return Albaran::findOrFail($cobro->albaran_id)->id;
    }

}
