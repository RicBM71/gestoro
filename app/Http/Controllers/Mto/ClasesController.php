<?php

namespace App\Http\Controllers\Mto;

use App\Clase;
use App\Grupo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClasesController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = Clase::with(['grupo'])->get();

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
        $this->authorize('create', Clase::class);

        if (request()->wantsJson())
        return [
            'grupos'=>Grupo::selGrupos(),
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

        $this->authorize('create', Clase::class);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'grupo_id' => ['required','integer', 'min:1'],

        ]);

        $data['username'] = session('username');

        $reg = Clase::create($data);

        if (request()->wantsJson())
            return ['clase'=>$reg, 'message' => 'EL registro ha sido creado'];
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
    public function edit(Clase $clase)
    {
        $this->authorize('update', $clase);

        if (request()->wantsJson())
            return [
                'grupos'=>Grupo::selGrupos(),
                'clase' =>$clase
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Clase $clase)
    {
        $this->authorize('update', $clase);

        $data = $request->validate([
            'grupo_id' => ['required','integer', 'min:1'],
            'nombre' => ['required', 'string', 'max:50'],
            'peso' => ['boolean'],
            'grabaciones' => ['boolean'],
            'quilates' => ['boolean'],
        ]);

        $data['username'] = $request->user()->username;


        $clase->update($data);

        if (request()->wantsJson())
            return ['clase'=>$clase, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Clase $clase)
    {
        $this->authorize('delete', $clase);

        $clase->delete();

        if (request()->wantsJson()){
            return response()->json(Clase::with(['grupo'])->get());
        }
    }
}
