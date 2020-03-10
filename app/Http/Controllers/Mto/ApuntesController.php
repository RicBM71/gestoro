<?php

namespace App\Http\Controllers\Mto;

use App\Apunte;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApuntesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = Apunte::all();

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
        //$this->authorize('create', new Apunte);

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
        $data['empresa_id'] =  session()->get('empresa')->id;

        $reg = Apunte::create($data);

        if (request()->wantsJson())
            return ['apunte'=>$reg, 'message' => 'EL registro ha sido creado'];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Apunte $apunte)
    {

        if (request()->wantsJson())
            return [
                'colores'=> Apunte::selColores(),
                'apunte' => $apunte
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Apunte $apunte)
    {

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'color' => ['nullable', 'string'],
        ]);

        $data['username'] = $request->user()->username;

        $apunte->update($data);

        if (request()->wantsJson())
            return ['apunte'=>$apunte, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Apunte $apunte)
    {

        if (esAdmin()){
            return abort(403,auth()->user()->name.' NO tiene permiso de administrador');
        }

        $apunte->delete();

        if (request()->wantsJson()){
            return response()->json(Apunte::get());
        }
    }
}
