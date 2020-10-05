<?php

namespace App\Http\Controllers\Mto;

use App\Marca;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MarcasController extends Controller
{
    public function index()
    {

        $data = Marca::all();

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

        if (request()->wantsJson())
        return [

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
        $this->authorize('create', Marca::class);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
        ]);

        $data['username'] = session('username');

        $reg = Marca::create($data);

        if (request()->wantsJson())
            return ['marca'=>$reg, 'message' => 'EL registro ha sido creado'];
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
    public function edit(Marca $marca)
    {
        $this->authorize('update', $marca);

        if (request()->wantsJson())
            return [
                'marca' =>$marca
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Marca $marca)
    {
        $this->authorize('update', $marca);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
        ]);

        $data['username'] = $request->user()->username;


        $marca->update($data);

        if (request()->wantsJson())
            return ['marca'=>$marca, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Marca $marca)
    {
        $this->authorize('delete', $marca);

        $marca->delete();

        if (request()->wantsJson()){
            return response()->json(Marca::get());
        }
    }
}
