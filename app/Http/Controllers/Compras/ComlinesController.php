<?php

namespace App\Http\Controllers\Compras;

use App\Clase;
use App\Compra;
use App\Comline;
use App\Quilate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\StoreComline;

class ComlinesController extends Controller
{
     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load(Request $request)
    {

        $data = $request->validate([
            'compra_id' => ['required', 'integer'],
            'grupo_id'  => ['required', 'integer']
        ]);

         if (request()->wantsJson())
            return [
                'clases'     => Clase::selClases($data['grupo_id']),
                'lineas'     => Comline::with('clase')->CompraId($data['compra_id'])->get(),
                'totales'    => Comline::totalCompra($data['compra_id']),
                'quilates'   => Quilate::selQuilates()
            ];
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreComline $request)
    {
        $data = $request->validated();

        if ($request->get('peso_gr') > 0)
            $data['importe_gr'] = setImporteGr($data['peso_gr'],$data['importe']);
        else
            $data['importe_gr'] = 0;

        $data['retencion'] = session()->get('parametros')->retencion;
        $data['empresa_id'] =  session()->get('empresa')->id;
        $data['username'] = $request->user()->username;

        if (is_null($request->get('peso_gr')))
            unset($data['peso_gr']);
        if (is_null($request->get('quilates')))
            unset($data['quilates']);


        $reg = Comline::create($data);

        if (request()->wantsJson())
            return [
                'lineas' => Comline::CompraId($reg->compra_id)->get(),
                'totales' => Comline::totalCompra($reg->compra_id)
            ];

    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreComline $request, Comline $comline)
    {

        $data = $request->validated();

        $data['username'] = $request->user()->username;
        $data['importe_gr'] = setImporteGr($data['peso_gr'],$data['importe']);

        if (is_null($request->get('peso_gr')))
            unset($data['peso_gr']);
        if (is_null($request->get('quilates')))
            unset($data['quilates']);


        $comline->update($data);

        if (request()->wantsJson())
            return [
                'lineas' => Comline::CompraId($comline->compra_id)->get(),
                'totales' => Comline::totalCompra($comline->compra_id),
                'compra'  => Compra::findOrFail($comline->compra_id)
           ];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comline $comline)
    {
       // $this->authorize('delete', $comline);

        $comline->delete();

        if (request()->wantsJson())
            return [
                'lineas' => Comline::CompraId($comline->compra_id)->get(),
                'totales' => Comline::totalCompra($comline->compra_id)
           ];
    }
}
