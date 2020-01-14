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

        if (esRoot())
            $data = Apunte::all();
        else
            $data = Apunte::libres()->get();

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

        if ($apunte->id <= 30 && !esRoot()){
            return abort(403,'NO se puede editar este registro!');
        }


        if (request()->wantsJson())
            return [
                'apunte' =>$apunte
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
        if ($apunte->id <= 30){
            return abort(403,'NO se puede borrar este registro!');
        }

        if (esRoot()){
            return abort(403,auth()->user()->name.' NO tiene permiso de root');
        }

        $apunte->delete();

        if (request()->wantsJson()){
            return response()->json(Apunte::libres()->get());
        }
    }
}