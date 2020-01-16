<?php

namespace App\Http\Controllers\Mto;

use App\Cruce;
use App\Empresa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CrucesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = Cruce::with('destino')->get();

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
        $this->authorize('create', Cruce::class);

        if (request()->wantsJson())
            return [
                'empresas' => Empresa::selEmpresas()->get(),
            ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->authorize('create', Cruce::class);

        $data = $request->validate([
            'comven'             => ['required', 'string', 'max:1'],
            'destino_empresa_id' => ['required', 'integer'],
        ]);

        $data['username'] = $request->user()->username;
        $data['empresa_id'] = session('empresa_id');

        $reg = Cruce::create($data);

        if (request()->wantsJson())
            return ['cruce'=>$reg, 'message' => 'EL registro ha sido creado'];
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Cruce $cruce)
    {
        $this->authorize('update', $cruce);

        if (request()->wantsJson())
            return [
                'cruce' =>$cruce,
                'empresas' => Empresa::selEmpresas()->get(),
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Grupo $cruce)
    {
        $this->authorize('update', $cruce);

        $data = $request->validate([
            'comven'             => ['required', 'string', 'max:1'],
            'destino_empresa_id' => ['required', 'integer'],
        ]);

        $data['username'] = $request->user()->username;


        $cruce->update($data);

        if (request()->wantsJson())
            return ['cruce'=>$cruce, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cruce $cruce)
    {
        $this->authorize('delete', $cruce);

        $cruce->delete();

        if (request()->wantsJson()){
            return response()->json(Cruce::all());
        }
    }
}
