<?php

namespace App\Http\Controllers\Mto;

use App\Fpago;
use App\Clidoc;
use App\Cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientes;
use App\Http\Requests\UpdateClientes;

class ClientesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (request()->session()->has('filtro_cli'))
            return $this->seleccionar();

        if (request()->wantsJson())
            return Cliente::orderBy('id','desc')
                    ->where('updated_at','>=',Carbon::now()->subDays(30))
                    ->get()
                    ->take(50);

    }


/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function filtrar(Request $request)
    {

        $data = $request->validate([
            'razon' => ['string','nullable'],
            'dni'   => ['string','nullable'],
            'baja' => ['string','nullable'],
            'notas' => ['string','nullable'],
            'bloqueado'=> ['string','nullable', 'max:1'],
        ]);

        session(['filtro_cli' => $data]);

        if (request()->wantsJson()){
            return $this->seleccionar();
        }

    }

    /**
     *  @param array $data // condiciones where genéricas
     *  @param array $doc  // condiciones para documentos
     */
    private function seleccionar(){

        $data = session('filtro_cli');

        return Cliente::dni($data['dni'])
                        ->razon($data['razon'])
                        ->bloqueado($data['bloqueado'])
                        ->notas($data['notas'])
                        ->baja($data['baja'])
                        ->orderby('razon')
                        ->get()
                        ->take(500);


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
                'fpagos'=> Fpago::selFPagos(),
            ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientes $request)
    {
        $data = $request->validated();

        $data['empresa_id'] = session()->get('empresa')->id;
        $data['cliente_empresa_id'] = session()->get('empresa')->cliente_empresa_id;

        $data['username'] = $request->user()->username;


        $reg = Cliente::create($data);

        if (request()->wantsJson())
            return ['cliente'=>$reg, 'message' => 'EL registro ha sido creado'];
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Cliente $cliente)
    {
        //$this->authorize('update', $cliente);
        if (request()->wantsJson())
            return [
                'cliente' =>$cliente,
                'fpagos'=> Fpago::selFPagos(),
            ];

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Cliente $cliente)
    {
        $this->authorize('update', $cliente);

        $docu = Clidoc::getDocumentos($cliente->id,$cliente->fecha_dni);


        if (request()->wantsJson())
            return [
                'cliente' =>$cliente,
                'fpagos'=> Fpago::selFPagos(),
                'documentos'=> $docu,
            ];

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientes $request, Cliente $cliente)
    {

        $this->authorize('update', $cliente);

        $data = $request->validated();


        $data['username'] = $request->user()->username;

        $cliente->update($data);

        if (request()->wantsJson())
            return [
                'cliente'=>$cliente,
                'message' => 'EL cliente ha sido modificado'
                ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cliente $cliente)
    {

        $this->authorize('delete', $cliente);

        $cliente->delete();


        if (request()->wantsJson()){
            return Cliente::orderBy('id','desc')
                    ->where('updated_at','>=',Carbon::now()->subDays(30))
                    ->get()
                    ->take(50);
        }
    }

    public function obs(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'notas'  => ['string', 'nullable'],
        ]);

        $data['username'] = $request->user()->username;

        $cliente->update($data);

        if (request()->wantsJson())
            return [
                'message' => 'EL registro ha sido modificado'
            ];
    }
}
