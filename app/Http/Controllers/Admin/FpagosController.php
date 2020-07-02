<?php

namespace App\Http\Controllers\Admin;

use App\Fpago;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FpagosController extends Controller
{
/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = Fpago::all();

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
        //$this->authorize('create', new Fpago);

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

        $data['empresa_id'] = session()->get('empresa')->comun_empresa_id;
        $data['username'] = $request->user()->username;

        $reg = Fpago::create($data);

        if (request()->wantsJson())
            return ['fpago'=>$reg, 'message' => 'EL registro ha sido creado'];
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
    public function edit(Fpago $fpago)
    {
        

        if (request()->wantsJson())
            return [
                'fpago' =>$fpago
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fpago $fpago)
    {


        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
        ]);

        $data['username'] = $request->user()->username;


        $fpago->update($data);

        if (request()->wantsJson())
            return ['fpago'=>$fpago, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fpago $fpago)
    {


        $fpago->delete();

        if (request()->wantsJson()){
            return response()->json(Fpago::all());
        }
    }
}
