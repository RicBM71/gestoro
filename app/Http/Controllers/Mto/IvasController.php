<?php

namespace App\Http\Controllers\Mto;

use App\Iva;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IvasController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = Iva::all();

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
        //$this->authorize('create', new iva);



    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

     //   $this->authorize('create', new iva);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'leyenda'=> ['nullable', 'string']
        ]);

        $data['username'] = $request->user()->username;

        $reg = Iva::create($data);

        if (request()->wantsJson())
            return ['iva'=>$reg, 'message' => 'EL registro ha sido creado'];
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Iva $iva)
    {
       // $this->authorize('update', $iva);

        if (request()->wantsJson())
            return [
                'iva' =>$iva
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Iva $iva)
    {
       // $this->authorize('update', $iva);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'rebu'  => ['required', 'boolean'],
            'importe'   => ['required', 'numeric'],
            'leyenda'=> ['nullable', 'string']
        ]);

        $data['username'] = $request->user()->username;


        $iva->update($data);

        if (request()->wantsJson())
            return ['iva'=>$iva, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Iva $iva)
    {
       // $this->authorize('delete', $iva);

        $iva->delete();

        if (request()->wantsJson()){
            return response()->json(Iva::all());
        }
    }
}
