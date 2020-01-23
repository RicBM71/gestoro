<?php

namespace App\Http\Controllers\Compras;


use App\Tipo;
use App\Libro;
use App\Clidoc;
use App\Compra;
use App\Empresa;
use App\Concepto;
use App\Deposito;
use Carbon\Carbon;
use App\Scopes\EmpresaScope;
use Illuminate\Http\Request;
use App\Exports\ComprasExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\Compras\ReabrirLoteRule;
use App\Http\Requests\Compras\StoreCompra;
use App\Http\Requests\Compras\UpdateCompra;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\Compras\FiltrarRequest;

class ComprasController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
                //->where('fecha_compra','>=',Carbon::today()->subDays(30))
        if (request()->session()->has('filtro_com')){
            $data = $this->miFiltro();
        }else{
            $data = Compra::with(['cliente','grupo','tipo','fase'])
                ->where('fecha_compra','=',Carbon::today())
                ->get();

        }

        if (request()->wantsJson())
            return $data;
    }

    public function filtrar(FiltrarRequest $request)
    {

        $data = $request->validated();

        session(['filtro_com' => $data]);

        return $this->miFiltro();

    }

    private function miFiltro(){

        $data = request()->session()->get('filtro_com');

        if ($data['retraso'] > 0){
            $collection = Compra::with(['cliente','grupo','tipo','fase'])
                            ->tipo(1)
                            ->grupo($data['grupo_id'])
                            ->fase($data['fase_id'])
                            ->get();

            $items =  $collection->whereBetween('retraso', [$data['retraso'], 99999]);

            $k=array();
            foreach ($items as $row){
                $k[]=$row;
            }

            return $k;
        }elseif($data['vivos']){

            return Compra::with(['cliente','grupo','tipo','fase'])
                            ->tipo(1)
                            ->grupo($data['grupo_id'])
                            ->fase(4)
                            ->get();

        }
        else
            return Compra::with(['cliente','grupo','tipo','fase'])
                            ->tipo($data['tipo_id'])
                            ->fecha($data['fecha_d'],$data['fecha_h'],$data['quefecha'])
                            ->grupo($data['grupo_id'])
                            ->fase($data['fase_id'])
                            ->get();

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $this->authorize('create', Compra::class);

        // if (!auth()->user()->hasPermissionTo('addcom')){
        //     return abort(422,auth()->user()->name.' NO tiene permiso para crear compras');
        // }

        if (request()->wantsJson())
            return [
                'libros' => Libro::selLibrosByEjercicio(Carbon::today()->format('Y')),
                'tipos'  => Tipo::selTiposCom()
            ];

    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCompra $request)
    {

        $this->authorize('create', Compra::class);

        $data = $request->validated();

        $fecha_compra = Carbon::parse($request->fecha_compra);
        $ejercicio    = $fecha_compra->format('Y');

        //$ejercicio = substr($request->fecha_compra,0,4);

        $contador = Libro::incrementaContador($ejercicio, $request->grupo_id, $request->albaran);
        //\Log::info($contador);


        $data['ejercicio']    = $ejercicio;
        $data['albaran']      = $contador['albaran'];
        $data['serie_com']    = $contador['serie_com'];
        $data['interes']      = $contador['interes'];
        $data['dias_custodia'] = $contador['dias_custodia'];

        $data['fecha_bloqueo'] = Compra::Bloqueo($request->fecha_compra, $contador['semdia_bloqueo']);

        if ($data['tipo_id']==1){
            $data['fecha_renovacion'] = $fecha_compra->addDays($contador['dias_custodia']);
            $data['importe_renovacion'] = 0;//round($compra->importe * $compra->interes / 100, 0);
        }else{
            $data['fecha_renovacion'] = null;
            $data['importe_renovacion'] = 0;
            $data['retencion'] = session()->get('parametros')->retencion;
        }

        $data['empresa_id'] =  session()->get('empresa')->id;
        $data['username']   = $request->user()->username;


        $reg = Compra::create($data);

        if (request()->wantsJson())
            return [
                'compra'=>$reg,
                'contador'=> $contador,
                'message' => 'EL registro ha sido creado'
            ];
    }

      /**
     * Show
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $compra = Compra::with(['cliente','grupo','grupo','tipo','fase'])->findOrFail($id);

        try {
            $libro = Libro::where('grupo_id', $compra->grupo_id)
                        ->where('ejercicio', getEjercicio($compra->fecha_compra))
                        ->firstOrFail();
            $grabaciones = $libro->grabaciones;
            $dias_cortesia = $libro->dias_cortesia;
        } catch (\Exception $e) {
            $grabaciones = false;
            $dias_cortesia = 7;
        }


        if (request()->wantsJson())
            return [
                'valor_compras'     => Deposito::valorCompras($compra->fecha_compra,$compra->cliente_id,$compra->id),
                'conceptos'         => Concepto::selConceptosC()->depositos()->get(),
                'compra'            => $compra,
                'documentos'        => Clidoc::getDocumentos($compra->cliente->id,$compra->cliente->fecha_dni,$compra->fecha_compra),
                'lineas_deposito'   => Deposito::CompraId($compra->id)->get(),
                'grabaciones'       => $grabaciones,
                'dias_cortesia'     => $dias_cortesia
            ];

    }


      /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

       $compra = Compra::with(['cliente','grupo','grupo','tipo','fase'])->findOrFail($id);

       $this->authorize('update', $compra);

       $libro = Libro::where('grupo_id', $compra->grupo_id)
                    ->where('ejercicio', getEjercicio($compra->fecha_compra))
                    ->firstOrFail();

    //    $ejercicio    = $compra->fecha_compra->format('Y');
    //    $contador = Libro::incrementaContador($ejercicio, 1, 1);

    //    print_r($contador);

    //    $fecha_compra = Carbon::parse($compra->fecha_compra);
    //    return $fecha_compra->addDays(30);

        // $this->Bloqueo($compra->fecha_compra);
      //  echo $this->Bloqueo("2019-09-07", "3/1");

        if (request()->wantsJson())
            return [
                'valor_compras'     => Deposito::valorCompras($compra->fecha_compra,$compra->cliente_id,$compra->id),
                'conceptos'         => Concepto::selConceptosC()->depositos()->get(),
                'compra'            => $compra,
                'documentos'        => Clidoc::getDocumentos($compra->cliente->id,$compra->cliente->fecha_dni,$compra->fecha_compra),
                'lineas_deposito'   => Deposito::CompraId($compra->id)->get(),
                'grabaciones'       => $libro->grabaciones,
                'dias_cortesia'     => $libro->dias_cortesia
            ];

    }

  /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCompra $request, Compra $compra)
    {
        $this->authorize('update', $compra);

        $data = $request->validated();
        $fecha_compra = Carbon::parse($data['fecha_compra']);


        $ejercicio    = $fecha_compra->format('Y');

        $contador = Libro::incrementaContador($ejercicio, $request->grupo_id, $request->albaran);

        $data['ejercicio']    = $ejercicio;
        $data['albaran']      = $contador['albaran'];
        $data['serie_com']    = $contador['serie_com'];

        $data['fecha_bloqueo'] = Compra::Bloqueo($request->fecha_compra, $contador['semdia_bloqueo']);

        if ($data['tipo_id']==1){
            if ($compra->interes <> $data['interes']){
              $data['retencion'] = 0;
              $data['importe_renovacion'] = round($compra->importe * $data['interes'] / 100, 0);
            }
        }else{
            $data['fecha_renovacion'] = null;
            $data['importe_renovacion'] = 0;
            $data['retencion'] = session()->get('parametros')->retencion;
        }

        // if ($data['tipo_id']==1){
        //     //if (is_null($compra->fecha_renovacion) || $compra->fecha_compra <> $data['fecha_compra']){
        //     //if (is_null($compra->fecha_renovacion)){
        //         $data['fecha_renovacion'] = $fecha_compra->addDays($data['dias_custodia']);
        //         $data['importe_renovacion'] = round($compra->importe * $compra->interes / 100, 0);
        //     //}
        // }else{

        //     $data['fecha_renovacion'] = null;
        //     $data['importe_renovacion'] = 0;

        // }

        $data['username'] = $request->user()->username;

        $compra->update($data);

        $compra = Compra::with(['cliente','grupo','tipo','fase'])->findOrFail($compra->id);

        if (request()->wantsJson())
            return [
                'compra'=>$compra,
                'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Compra $compra)
    {

        $this->authorize('delete', $compra);

        $compra->delete();

        $contador = Libro::restaContadorCompra($compra->ejercicio, $compra->grupo_id, $compra->albaran);

        if (request()->wantsJson()){
            return  $contador;
        }
    }

    public function obs(Request $request, Compra $compra)
    {
        $data = $request->validate([
            'notas'      => ['string', 'nullable'],
        ]);

        // no le dejo porque no afecta y porque si no desbloquea el permiso propietario
        $data['username'] = $request->user()->username;

        $compra->update($data);

        $compra = Compra::with(['cliente','grupo','tipo','fase'])->findOrFail($compra->id);

        if (request()->wantsJson())
            return [
                'compra'=>$compra,
                'message' => 'EL registro ha sido modificado'
            ];
    }

    /**
     * Alterna cambio entre compra y recompra
     *
     * @param Request $request
     * @param Compra $compra
     * @return void
     */
    public function tipo(Request $request, Compra $compra)
    {
        $data = $request->validate([
            'tipo_id'       => ['integer', 'required'],
        ]);

        if (!in_array($compra->fase_id,[3,4])){
            return abort(411,'La compra no está en depósito');;
        }

        $fecha_compra = Carbon::parse($compra->fecha_compra);

        $totales_concepto = Deposito::totalesConcepto($compra->id);

        if ($data['tipo_id']==1){
            if ($totales_concepto[1] == 0)
                $data['fecha_renovacion'] = $fecha_compra->addDays($compra->dias_custodia);
            $data['importe_renovacion'] = round($compra->importe * $compra->interes / 100, 0);
        }else{
            if ($totales_concepto[1] == 0){
                $data['fecha_renovacion'] = null;
                $data['importe_renovacion'] = 0;
            }
        }

        $data['username'] = $request->user()->username;

        $compra->update($data);

        if (request()->wantsJson())
            return [
                'message' => 'EL registro ha sido modificado'
            ];
    }

    public function fase(Request $request, Compra $compra)
    {
        $data = $request->validate([
            'fase_id'      => ['integer', 'required', new ReabrirLoteRule($compra)],
        ]);

        $data['username'] = $request->user()->username;

        $compra->update($data);

        if (request()->wantsJson())
            return [
                'message' => 'EL registro ha sido modificado'
            ];
    }

    public function recogida(Request $request, Compra $compra)
    {
        $data = $request->validate([
            'fecha_recogida'      => ['nullable', 'date'],
        ]);

        // no le dejo porque no afecta y porque si no desbloquea el permiso propietario
        $data['username'] = $request->user()->username;

        $compra->update($data);

        if (request()->wantsJson())
            return [
                'compra' => Compra::with(['cliente','grupo','tipo','fase'])->findOrFail($compra->id),
                'message' => 'Se ha modificado la fecha de recogida'
            ];
    }

     /**
     * Recibe las facturas por request, previamente de $this->lisfac()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new ComprasExport($request->data), 'compras.xlsx');

    }


}
