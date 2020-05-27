<?php

namespace App\Http\Controllers\Mto;

use App\Grupo;
use App\Libro;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Rules\LibroUniqueRule;
use App\Http\Controllers\Controller;

class LibrosController extends Controller
{


     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (request()->session()->has('filtro_lib'))
            return $this->seleccionar();

        if (request()->wantsJson())
            return Libro::with(['grupo'])->get();

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function filtrar(Request $request)
    {

        $data = $request->validate([
            'ejercicio' => ['required','integer','max:2100']
        ]);

        session(['filtro_lib' => $data]);

        if (request()->wantsJson()){
            return $this->seleccionar();
        }

    }

    /**
     *  @param array $data // condiciones where genéricas
     *  @param array $doc  // condiciones para documentos
     */
    private function seleccionar(){

        $data = session('filtro_lib');

        return Libro::with(['grupo'])->ejercicio($data['ejercicio'])->get();

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
                'grupos' => Grupo::selGruposRebu()
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

        $this->authorize('create', new Libro);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'ejercicio'=> ['required','integer'],
            'grupo_id' => ['required', new LibroUniqueRule($request->ejercicio)]
        ]);


        //TODO: quitar requerido y en update CREAR rule para que sea única por empresa, por lomenos srie compra
        if ($data['grupo_id'] == 1){
            $data['serie_com'] =  "M";
            $data['serie_fac'] =  "RF";
        }else{
            $data['serie_com'] =  "U";
            $data['serie_fac'] =  "RU";
        }
        $data['empresa_id'] =  session()->get('empresa')->id;
        $data['username'] = $request->user()->username;

        $reg = Libro::create($data);

        if (request()->wantsJson())
            return ['libro'=>$reg, 'message' => 'EL registro ha sido creado'];
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
    public function edit(Libro $libro)
    {
        $this->authorize('update', $libro);

        if (request()->wantsJson())
            return [
                'libro' =>$libro,
                'grupos' => Grupo::selGruposRebu()
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Libro $libro)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'grupo_id' => ['required', new LibroUniqueRule($libro->ejercicio, $libro->id)],
            'ejercicio'=> ['required','integer'],
            'ult_compra'=> ['required','integer'],
            'ult_factura'=> ['required','integer'],
            'serie_fac'=> ['required'],
            'serie_com'=> ['required'],
            'grabaciones'=> ['required'],
            'recompras'=> ['required'],
            'peso_frm'=> ['required'],
            'cerrado'=> ['required'],
            'semdia_bloqueo'=> ['required','string','max:3'],
            'dias_custodia'=> ['required','integer',],
            'interes'=> ['required', 'numeric'],
            'interes_recuperacion'=> ['required', 'numeric'],
            'codigo_pol'=> ['string','nullable'],
            'nombre_csv'=> ['string','nullable'],

        ]);

        $data['username'] = $request->user()->username;

        if (session('parametros')->doble_interes == false)
            $data['interes_recuperacion'] = $data['interes'];


        $libro->update($data);

        if (request()->wantsJson())
            return ['libro'=>$libro, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Libro $libro)
    {
        $this->authorize('delete', $libro);

        $libro->delete();

        if (request()->wantsJson()){
            return response()->json(Libro::with(['grupo'])->where('ejercicio',date('Y'))->get());
        }
    }
}
