<?php

namespace App\Http\Controllers\Mto;

use App\Tipo;
use App\Contador;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContador;
use App\Http\Requests\UpdateContador;

class ContadoresController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (request()->session()->has('filtro_con'))
            return $this->seleccionar();

        if (request()->wantsJson())
            return Contador::with('tipo')->get();
            //return Contador::abierto()->get();

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

        session(['filtro_con' => $data]);

        if (request()->wantsJson()){
            return $this->seleccionar();
        }

    }

    /**
     *  @param array $data // condiciones where genéricas
     *  @param array $doc  // condiciones para documentos
     */
    private function seleccionar(){

        $data = session('filtro_con');


        return Contador::ejercicio($data['ejercicio'])->with('tipo')->get();

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Contador::class);

        if (request()->wantsJson())
            return [
                'tipos' => Tipo::selTiposVen()
            ];

    }

      /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContador $request)
    {

        $this->authorize('create', Contador::class);

        $data = $request->validated();

        if ($data['tipo_id'] == 3){
            $sa = 'R';
            $sf = 'F';
            $sfa = 'FA';
            $sfr = 'FR';
        }elseif($data['tipo_id'] == 4){
            $sa = 'V';
            $sf = 'G';
            $sfa = 'GA';
            $sfr = 'GR';
        }elseif($data['tipo_id'] == 5){
            $sa = 'T';
            $sf = 'FT';
            $sfa = 'TA';
            $sfr = 'TR';
        }

        $data['serie_albaran']=$sa;
        $data['serie_factura']=$sf;
        $data['serie_factura_auto']=$sfa;
        $data['serie_factura_abono']=$sfr;

        $data['empresa_id'] =  session()->get('empresa')->id;
        $data['username']   = $request->user()->username;

        $reg = Contador::create($data);

        if (request()->wantsJson())
            return ['contador'=>$reg, 'message' => 'EL registro ha sido creado'];
    }

      /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Contador $contadore)
    {
        $this->authorize('update', $contadore);

        if (request()->wantsJson())
            return [
                'contador' =>$contadore,
                'tipos' => Tipo::selTiposVen()
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContador $request, Contador $contadore)
    {
        $this->authorize('update', $contadore);

        $data = $request->validated();

        $data['username'] = $request->user()->username;

        $contadore->update($data);

        if (request()->wantsJson())
            return ['contador'=>$contadore, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contador $contadore)
    {
        $this->authorize('delete', $contadore);

        $contadore->delete();

        if (request()->wantsJson()){
            return response()->json(Contador::with('tipo')->abierto()->get());
        }
    }
}
