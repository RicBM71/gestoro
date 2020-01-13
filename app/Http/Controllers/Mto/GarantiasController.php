<?php

namespace App\Http\Controllers\Mto;

use App\Garantia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GarantiasController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = Garantia::all();

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
        //$this->authorize('create', new garantia);

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

        $data['username'] = $request->user()->username;

        $reg = Garantia::create($data);

        if (request()->wantsJson())
            return ['garantia'=>$reg, 'message' => 'EL registro ha sido creado'];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Garantia $garantia)
    {

        if (request()->wantsJson())
            return [
                'garantia' =>$garantia
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Garantia $garantia)
    {

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'leyenda' => ['required', 'string'],
        ]);

        $data['username'] = $request->user()->username;

        $garantia->update($data);

        if (request()->wantsJson())
            return ['garantia'=>$garantia, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Garantia $garantia)
    {
        
        if (!auth()->user()->hasRole('Admin')){
            return abort(403,auth()->user()->name.' NO tiene permiso de administrador');
        }

        $garantia->delete();

        if (request()->wantsJson()){
            return response()->json(Garantia::all());
        }
    }
}
