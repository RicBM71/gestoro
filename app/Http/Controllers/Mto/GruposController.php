<?php

namespace App\Http\Controllers\Mto;

use App\Grupo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GruposController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = Grupo::all();

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
        $this->authorize('create', Grupo::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->authorize('create', Grupo::class);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],

        ]);

        $data['leyenda'] = $data['nombre'];
        $data['username'] = $request->user()->username;

        $reg = Grupo::create($data);

        if (request()->wantsJson())
            return ['grupo'=>$reg, 'message' => 'EL registro ha sido creado'];
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Grupo $grupo)
    {
        $this->authorize('update', $grupo);

        if (request()->wantsJson())
            return [
                'grupo' =>$grupo
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Grupo $grupo)
    {
        $this->authorize('update', $grupo);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:30'],
            'leyenda' => ['required', 'string', 'max:50'],
            'rebu' => ['required','boolean'],
        ]);

        $data['username'] = $request->user()->username;


        $grupo->update($data);

        if (request()->wantsJson())
            return ['grupo'=>$grupo, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Grupo $grupo)
    {
        $this->authorize('delete', $grupo);

        $grupo->delete();

        if (request()->wantsJson()){
            return response()->json(Grupo::all());
        }
    }
}
