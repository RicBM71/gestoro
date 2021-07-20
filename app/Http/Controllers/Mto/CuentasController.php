<?php

namespace App\Http\Controllers\Mto;

use App\Cuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CuentasController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = Cuenta::all();

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
        $this->authorize('create', Cuenta::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->authorize('create', Cuenta::class);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
        ]);

        $data['empresa_id'] = session('empresa')->id;
        $data['username'] = $request->user()->username;

        $reg = Cuenta::create($data);

        if (request()->wantsJson())
            return ['cuenta'=>$reg, 'message' => 'EL registro ha sido creado'];
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Cuenta $cuenta)
    {
        $this->authorize('update', $cuenta);

        if (request()->wantsJson())
            return [
                'cuenta' =>$cuenta
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cuenta $cuenta)
    {
        $this->authorize('update', $cuenta);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'iban'   => ['nullable','iban', 'max:50'],
            'bic'    => ['nullable','bic', 'max:11'],
            'sufijo_adeudo' => ['nullable','string', 'max:20'],
            'sufijo_transf' => ['nullable','string', 'max:20'],
            'defecto'=> ['boolean'],
            'activa' => ['boolean'],
        ]);

        $data['username'] = $request->user()->username;

        if ($data['defecto']){
            DB::table('cuentas')
                ->where('empresa_id',session('empresa_id'))
                ->where('id', '<>', $cuenta->id)
                ->update(['defecto' => false]);
        }


        $cuenta->update($data);

        if (request()->wantsJson())
            return ['cuenta'=>$cuenta, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cuenta $cuenta)
    {
       // $this->authorize('delete', $cuenta);

        $cuenta->delete();

        if (request()->wantsJson()){
            return response()->json(Cuenta::all());
        }
    }
}
