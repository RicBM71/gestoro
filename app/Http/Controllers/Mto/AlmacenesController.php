<?php

namespace App\Http\Controllers\Mto;

use App\Almacen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AlmacenesController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = Almacen::all();

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
        //$this->authorize('create', new Almacen);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->authorize('create', Almacen::class);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
        ]);

        $data['empresa_id'] = session()->get('empresa')->comun_empresa_id;
        $data['username'] = $request->user()->username;

        $reg = Almacen::create($data);

        if (request()->wantsJson())
            return ['almacen'=>$reg, 'message' => 'EL registro ha sido creado'];
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
    public function edit(Almacen $almacene)
    {
        $this->authorize('update', $almacene);

        if (request()->wantsJson())
            return [
                'almacen' =>$almacene
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Almacen $almacene)
    {
        $this->authorize('update', $almacene);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
        ]);

        $data['username'] = $request->user()->username;


        $almacene->update($data);

        if (request()->wantsJson())
            return ['almacen'=>$almacene, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Almacen $almacene)
    {
        $this->authorize('delete', $almacene);

        $almacene->delete();

        if (request()->wantsJson()){
            return response()->json(Almacen::all());
        }
    }
}
