<?php

namespace App\Http\Controllers\Compras;

use App\Tipo;
use App\Clase;
use App\Libro;
use App\Compra;
use App\Comline;
use App\Producto;
use Carbon\Carbon;
use App\Scopes\EmpresaScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LiquidarController extends Controller
{
    public function index(){

        if (!auth()->user()->hasPermissionTo('liquidar')){
            return abort(403,auth()->user()->name.' NO tiene permiso para liquidar');
        }

        if (request()->wantsJson())
            return [
                'tipos' => Tipo::selTiposCom(),
                'clases' => Clase::selGrupoClaseRebu(),
                'libros' => Libro::selLibrosByEjercicio(Carbon::today()->format('Y'))
            ];
    }

    public function preliquidado(Request $request){

        $data = $request->validate([
            'fecha_h'   => ['required','date'],
            'fecha_liq' => ['required','date'],
            'tipo_id'   => ['required','integer'],
            'clase_id'  => ['required','integer'],
        ]);

        if (request()->wantsJson())
            return [
                'compras' => Compra::obtenerLineasPreLiquidado($data['fecha_h'],$data['tipo_id'],$data['clase_id'])
            ];

    }

     /**
      * Funde las líneas previamente seleccionadas
      *
      * @param Request $request
      * @return void
      */
    public function masivo(Request $request){

        if (!auth()->user()->hasPermissionTo('liquidar')){
            return abort(403,auth()->user()->name.' NO tiene permiso para liquidar');
        }


        $data = $request->validate([
            'fecha_liq' => ['required','date'],
            'lineas'=> ['array']
        ]);

        foreach ($data['lineas'] as $linea) {

            $data_comline = [
                'fecha_liquidado' =>$data['fecha_liq'],
                'username'      =>session('username')
            ];

            Comline::setLiquidado($linea['id'],$data_comline);
            $this->setFaseLiquidado($linea['compra_id']);

        }


        if (request()->wantsJson())
            return [
                'ok'
            ];

    }

    /**
     *
     */
    public function edit($id){

        if (!auth()->user()->hasPermissionTo('liquidar')){
            return abort(403,auth()->user()->name.' NO tiene permiso para liquidar');
        }

        $compra = Compra::with(['cliente','grupo','tipo','fase'])->findOrFail($id);

        // TODO: Esto lo dejo por lotes traspasados de empresa EvaOro a Sol, lo debería quitar.
        $libro = Libro::where('grupo_id', $compra->grupo_id)
                        ->where('ejercicio', date('Y'))
                        ->firstOrFail();
        // $libro = Libro::withoutGlobalScope(EmpresaScope::class)
        //             ->where('grupo_id', $compra->grupo_id)
        //             ->where('ejercicio', getEjercicio($compra->fecha_compra))
        //             ->firstOrFail();

        $lineas = Comline::with('clase')->CompraId($compra->id)->get();
        $productos = Producto::withoutGlobalScope(EmpresaScope::class)->with('clase')->compraId($compra->id)->get();

        $peso_compra = $lineas->sum('peso_gr');
        $peso_inventario = $productos->sum('peso_gr');

        $peso_inventario = $peso_compra - $peso_inventario;

        if (request()->wantsJson())
            return [
                'compra'         => $compra,
                'lineas'         => $lineas,
                'peso_compra'    => $peso_compra,
                'productos'      => $productos,
                'peso_inventario'=> $peso_inventario,
                'grabaciones'    => $libro->grabaciones,
            ];
    }

    /**
     * Funde un solo lote
     */
    public function liquidar(Request $request){

        if (!auth()->user()->hasPermissionTo('liquidar')){
            return abort(403,auth()->user()->name.' NO tiene permiso para liquidar');
        }


        $data = $request->validate([
            'fecha'     => ['required','date'],
            'compra_id' => ['required','integer'],
            'lineas'    => ['array']
        ]);

        foreach ($data['lineas'] as $comline_id) {

            $comline = Comline::findOrfail($comline_id);

            if (is_null($comline->fecha_liquidado))
                $comline->update(['fecha_liquidado'=>$data['fecha']]);
            else
                $comline->update(['fecha_liquidado'=>null]);
        }

        $this->setFaseLiquidado($data['compra_id']);

        if (request()->wantsJson())
            return [
                'lineas' => Comline::with('clase')->CompraId($data['compra_id'])->get(),
               // 'productos'=> Producto::withoutGlobalScope(EmpresaScope::class)->compra($data['compra_id'])->get()
            ];

    }

    public function mostrar(Request $request){

        $data = $request->validate([
            'fecha_liq' => ['required','date'],
            'tipo_id'   => ['required','integer'],
            'clase_id'  => ['required','integer'],
        ]);

        $lineas = Comline::with(['clase','compra.productos'])
                    ->liquidados($data['fecha_liq'])
                    ->clase($data['clase_id'])
                    ->get();


        $lineas_con_filtro_tipo=[];
        foreach ($lineas as $comline){

            // if ($comline->compra == null)
            //     continue;

            if ($comline->compra->tipo_id == $data['tipo_id']){
                $lineas_con_filtro_tipo[]=$comline;
            }
        }

        // me lo guardo para otra, esta es más compleja al tener que buscar dentro de objeto compra.
        // $lineas_con_filtro_tipo = $lineas->where('tipo_id',  $data['tipo_id']);
        // $lineas_con_filtro_tipo->all()

        if (request()->wantsJson())
            return [
                'lineas' => $lineas_con_filtro_tipo
            ];

    }

    public function deshacer(Request $request){

        $data = $request->validate([
            'fecha_liq' => ['required','date'],
            'tipo_id'   => ['required','integer'],
            'clase_id'  => ['required','integer'],
        ]);


        $lineas = Comline::with(['compra'])
                    ->liquidados($data['fecha_liq'])
                    ->clase($data['clase_id'])
                    ->get();


        foreach($lineas as $comline){
                    // no puedo hacer update directo por este if
            if ($comline->compra->tipo_id == $data['tipo_id']){
                $comline->update(['fecha_liquidado'=>null,'username'=>session('username')]);
                $this->setFaseLiquidado($comline->compra_id);
            }
        }

        if (request()->wantsJson())
            return [
              'deshacer, liquidado'
            ];

    }

    /**
     *
     * Marca la fase de la compra como liquidado, parcialmente liquidado o depósito.
     *
     */
    private function setFaseLiquidado($compra_id)
    {
        $compra = Compra::with(['comlines'])->findOrFail($compra_id);

        $totales = $liquidadas = 0;
        foreach ($compra->comlines as $row){
            $totales++;
            if ($row->fecha_liquidado <> null)
                $liquidadas++;
        }

        if ($liquidadas == $totales)
            $fase_id = 7; // liquidado
        elseif ($liquidadas > 0)
            $fase_id = 6; // parcialmente liquidado
        else
            $fase_id = 4;  // depósito

        $data=[
            'fase_id'=> $fase_id,
            'username'=> session('username')
        ];

        $compra->update($data);
    }

    public function direct(Request $request){

        $data = $request->validate([
            'fecha_h'   => ['required','date'],
            'fecha_liq' => ['required','date'],
            'tipo_id'   => ['required','integer'],
            'clase_id'  => ['required','integer'],
        ]);

        $compras = Compra::obtenerLineasPreLiquidado($data['fecha_h'],$data['tipo_id'],$data['clase_id']);

        $lineas_id = $compras->pluck('id');

        if ($compras->count() == 0)
            return abort(404, 'No hay compras para liquidar');

        $data_comline = [
            'fecha_liquidado' =>$data['fecha_liq'],
            'updated_at'    => Carbon::now()->toDateTimeString(),
            'username'      =>session('username')
        ];

        // actualiza masiva las líneas.
        DB::table('comlines')
            ->where('empresa_id', session('empresa')->id)
            ->whereIn('id',$lineas_id)
            ->update($data_comline);


        foreach ($compras as $linea) {
            $this->setFaseLiquidado($linea->compra_id);
        }

        if (request()->wantsJson())
            return [
                'ok'
            ];


    }

}
