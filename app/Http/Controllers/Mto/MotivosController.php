<?php

namespace App\Http\Controllers\Mto;

use App\Motivo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MotivosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = Motivo::all();

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
        //$this->authorize('create', new Motivo);

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
            'nombre' => ['required', 'string', 'max:50'],

        ]);

        $reg = Motivo::create($data);

        if (request()->wantsJson())
            return ['motivo'=>$reg, 'message' => 'EL registro ha sido creado'];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Motivo $motivo)
    {

        if (request()->wantsJson())
            return [
                'motivo' =>$motivo
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Motivo $motivo)
    {

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
        ]);


        $motivo->update($data);

        if (request()->wantsJson())
            return ['motivo'=>$motivo, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Motivo $motivo)
    {

        $motivo->delete();

        if (request()->wantsJson()){
            return response()->json(Motivo::all());
        }
    }
}
