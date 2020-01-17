<?php

namespace App\Http\Controllers\Mto;

use App\Caja;
use App\Apunte;
use Carbon\Carbon;
use App\Exports\CajaExport;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use App\Http\Controllers\Controller;
use App\Rules\MaxDiasRangoFechaRule;
use Maatwebsite\Excel\Facades\Excel;

class CajasController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (request()->session()->has('filtro_caj'))
            return $this->seleccionar();
        else{
            if (request()->wantsJson()){
                $hoy = Carbon::now()->format('Y-m-d');

                return [
                    'caja'  => Caja::with('apunte')
                                        ->orderBy('fecha','desc')
                                        ->where('fecha','=',$hoy)
                                        ->get(),
                    'fecha_saldo'=> getFecha($hoy),
                    'saldo' => Caja::saldo($hoy)
                ];
            }
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function filtrar(Request $request)
    {

        $data = $request->validate([
            'quefecha'  => ['string','required'],
            'fecha_d'   => ['string','required',new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'   => ['string','required', new MaxDiasRangoFechaRule($request->fecha_d, $request->fecha_h)],
            'dh'        => ['string','required'],
            'apunte_id' => ['nullable','integer'],
          //  'manual' => ['string','required'],
        ]);

        session(['filtro_caj' => $data]);

        if (request()->wantsJson()){
            return $this->seleccionar();
        }

    }

    /**
     *  @param array $data // condiciones where genéricas
     *  @param array $doc  // condiciones para documentos
     */
    private function seleccionar(){

        $data = session('filtro_caj');

        return [
            'caja' => Caja::with('apunte')
                        ->rangoFechas($data['fecha_d'],$data['fecha_h'])
                        ->dh($data['dh'])
                        // ->manual($data['manual'])
                        ->apunte($data['apunte_id'])
                        ->orderby('fecha')
                        ->get()
                        ->take(500),
            'fecha_saldo'=> getFecha($data['fecha_h']),
            'saldo'=>Caja::saldo($data['fecha_h'])
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
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:190'],
            'importe' => ['required','numeric'],
            'manual' => ['required', 'string'],
            'fecha'=> ['required','date'],
            'dh'=> ['required','string'],
            'deposito_id'=> ['nullable','integer'],
            'cobro_id'=> ['nullable','integer'],
            'apunte_id'=> ['nullable','integer'],
        ]);

        $data['empresa_id'] =  session()->get('empresa')->id;
        $data['username']   = $request->user()->username;

        $reg = Caja::create($data);

        if (request()->wantsJson())
            return [
                'caja'=>$reg,
                'message' => 'EL registro ha sido creado'
            ];
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
                'apuntes' => Apunte::selApuntes()
            ];

    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Caja $caja)
    {
        $this->authorize('update', $caja);

        if (request()->wantsJson())
            return [
                'caja' =>$caja,
                'saldo'=>getCurrency(Caja::saldo($caja->fecha)),
                'apuntes' => Apunte::selApuntes()
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Caja $caja)
    {
        $this->authorize('update', $caja);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'importe' => ['required','numeric'],
            'fecha'=> ['required','date'],
            'dh'=> ['required','string'],
            'apunte_id'=> ['nullable','integer'],
        ]);

        $data['empresa_id'] = session()->get('empresa')->id;
        $data['username'] = $request->user()->username;


        $caja->update($data);

        if (request()->wantsJson())
            return [
                'caja'=>$caja,
                'saldo'=>getCurrency(Caja::saldo($caja->fecha)),
                'message' => 'EL registro ha sido modificado'
            ];
    }



     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Caja $caja)
    {

       $this->authorize('delete', $caja);

        $caja->delete();

        $hoy = Carbon::now()->format('Y-m-d');

        if (request()->wantsJson()){
            return [
                'caja'  => Caja::with('apunte')
                                ->where('fecha','=',$hoy)
                                ->orderBy('fecha','desc')
                                ->get(),
                'fecha_saldo'=> getFecha($hoy),
                'saldo' => Caja::saldo($hoy)
            ];
        }
    }

       /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saldo(Request $request)
    {
        $data = $request->validate([
            'fecha'=> ['required','date'],
        ]);

        if (request()->wantsJson())
            return [
                'saldo'=>Caja::saldo($data['fecha'])
            ];
    }

    public function excel(Request $request){

        if (!auth()->user()->hasRole('Admin'))
            return abort(403,auth()->user()->name.' no puede exportar');

        return Excel::download(new CajaExport($request->data), 'caja.xlsx');

    }


}
