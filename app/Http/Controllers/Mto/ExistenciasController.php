<?php

namespace App\Http\Controllers\Mto;

use App\Existencia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExistenciasController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = Existencia::all();

        if (request()->wantsJson())
            return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'detalle_id' => ['required', 'integer'],
            'importe' => ['required', 'numeric'],
        ]);

        $data['empresa_id'] = session()->get('empresa')->id;
        $data['username'] = $request->user()->username;

        $reg = Existencia::create($data);

        if (request()->wantsJson())
            return ['registro'=>$reg, 'message' => 'EL registro ha sido creado'];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id (id del archivo)
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Existencia $existencia)
    {

        if (request()->wantsJson())
            return [
                'registro' =>$existencia
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Existencia $existencia)
    {

        $this->authorize('update', $existencia);

        $data = $request->validate([
            'fecha'      => ['required', 'date'],
            'detalle_id' => ['required', 'integer'],
            'importe'    => ['required', 'numeric'],
        ]);

        $data['username'] = $request->user()->username;


        $existencia->update($data);

        if (request()->wantsJson())
            return ['existencia'=>$existencia, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Existencia $existencia)
    {

        $this->authorize('delete', $existencia);

        $existencia->delete();

        if (request()->wantsJson()){
            return response()->json(Existencia::all());
        }
    }
}
