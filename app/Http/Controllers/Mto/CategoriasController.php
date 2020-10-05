<?php

namespace App\Http\Controllers\Mto;

use App\Categoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriasController extends Controller
{
    public function index()
    {

        $data = Categoria::all();

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
        $this->authorize('create', Categoria::class);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
        ]);

        $data['username'] = session('username');

        $reg = Categoria::create($data);

        if (request()->wantsJson())
            return ['categoria'=>$reg, 'message' => 'EL registro ha sido creado'];
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
    public function edit(Categoria $categoria)
    {
        $this->authorize('update', $categoria);

        if (request()->wantsJson())
            return [
                'categoria' =>$categoria
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Categoria $categoria)
    {
        $this->authorize('update', $categoria);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
        ]);

        $data['username'] = $request->user()->username;


        $categoria->update($data);

        if (request()->wantsJson())
            return ['categoria'=>$categoria, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Categoria $categoria)
    {
        $this->authorize('delete', $categoria);

        $categoria->delete();

        if (request()->wantsJson()){
            return response()->json(Categoria::get());
        }
    }
}
