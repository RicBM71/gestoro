<?php

namespace App\Http\Controllers\Exportar;

use App\Tipo;
use App\Clase;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use App\Exports\CuadroMandoExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DetalleComprasExport;

class CuadroMandoController extends Controller
{
    public function index(){


        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        if (request()->wantsJson())
            return [
                'tipos' => Tipo::selTiposCom(),
                'clases' => Clase::selGrupoClase()
            ];

    }

    public function submit(Request $request){

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $request->validate([
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
            'totales'  => ['required','boolean'],
            'facturado'=> ['required','string'],
        ]);

        return [
            'comprados' => $this->comprado($data),
            'liquidados' => $this->liquidado($data),
            'inventariados' => $this->inventariados($data),
            'liquidado_neto'=> $this->liquidado_neto($data),
            'ventas'=> $this->ventas($data),
            'depositos'=> $this->depositos($data),
            'inventario' => $this->inventario($data),
        ];

    }

    private function comprado($data){

       $rollup = ($data['totales'] == true) ?  'WITH ROLLUP' : null;

        $select = DB::getTablePrefix().'tipos.nombre AS tipo,'.DB::getTablePrefix().'clases.nombre AS clase, '.DB::getTablePrefix().'comlines.quilates, SUM('.DB::getTablePrefix().'comlines.importe) AS importe, SUM('.DB::getTablePrefix().'comlines.peso_gr) AS peso_gr';

        $union0 = DB::table('compras')
            ->select(DB::raw($select))
            ->join('comlines','compras.id','=','comlines.compra_id')
            ->join('clases','clase_id','=','clases.id')
            ->join('tipos','tipo_id','=','tipos.id')
            ->where('compras.empresa_id', session('empresa')->id)
            ->whereDate('fecha_compra','>=', $data['fecha_d'])
            ->whereDate('fecha_compra','<=', $data['fecha_h'])
            ->groupBy(DB::raw('tipo,clase,quilates '.$rollup))
            ->get();


        return $union0;
    }

    private function liquidado($data){

        $rollup = ($data['totales'] == true) ?  'WITH ROLLUP' : null;

        $select = DB::getTablePrefix().'clases.nombre AS clase, '.DB::getTablePrefix().'comlines.quilates, SUM('.DB::getTablePrefix().'comlines.importe) AS importe, SUM('.DB::getTablePrefix().'comlines.peso_gr) AS peso_gr';

        $union0 = DB::table('compras')
            ->select(DB::raw($select))
            ->join('comlines','compras.id','=','comlines.compra_id')
            ->join('clases','clase_id','=','clases.id')
            ->where('compras.empresa_id', session('empresa')->id)
            ->whereDate('fecha_liquidado','>=', $data['fecha_d'])
            ->whereDate('fecha_liquidado','<=', $data['fecha_h'])
            ->groupBy(DB::raw('clase,quilates '.$rollup))
            ->get();


        return $union0;
    }

    private function inventariados($data){

        $rollup = ($data['totales'] == true) ?  'WITH ROLLUP' : null;

        $select = DB::getTablePrefix().'clases.nombre AS clase, '.DB::getTablePrefix().'productos.quilates, SUM('.DB::getTablePrefix().'productos.precio_coste) AS importe, SUM('.DB::getTablePrefix().'productos.peso_gr) AS peso_gr';

        $union0 = DB::table('productos')
            ->select(DB::raw($select))
            ->join('clases','clase_id','=','clases.id')
            ->where('productos.empresa_id', session('empresa')->id)
            ->where('compra_id','>', 0)
            ->where('estado_id','<', 5)
            ->whereDate('productos.created_at','>=', $data['fecha_d'])
            ->whereDate('productos.created_at','<=', $data['fecha_h'])
            ->whereNull('productos.deleted_at')
            ->groupBy(DB::raw('clase,quilates '.$rollup))
            ->get();


        return $union0;
    }

    private function liquidado_neto($data){

        $rollup = ($data['totales'] == true) ?  'WITH ROLLUP' : null;

        $select=DB::getTablePrefix().'clases.nombre AS clase, '.
                DB::getTablePrefix().'comlines.quilates AS quilates,'.
                'SUM('.DB::getTablePrefix().'comlines.importe) AS importe,'.
                'SUM('.DB::getTablePrefix().'comlines.peso_gr) AS peso_gr';

        $union1 = DB::table('compras')
            ->select(DB::raw($select))
                ->join('comlines','compras.id','=','compra_id')
                ->join('clases','comlines.clase_id','=','clases.id')
                ->where('compras.empresa_id', session('empresa')->id)
                ->whereDate('fecha_liquidado','>=', $data['fecha_d'])
                ->whereDate('fecha_liquidado','<=', $data['fecha_h'])
                ->groupBy(DB::raw('clase,quilates'));

        $select=DB::getTablePrefix().'clases.nombre AS clase, '.
                DB::getTablePrefix().'productos.quilates AS quilates,'.
                'SUM('.DB::getTablePrefix().'productos.precio_coste * -1) AS importe,'.
                'SUM('.DB::getTablePrefix().'productos.peso_gr * -1) AS peso_gr';


        $union2 = DB::table('productos')
            ->select(DB::raw($select))
                ->join('clases','productos.clase_id','=','clases.id')
                ->where('productos.empresa_id', session('empresa')->id)
                ->whereDate('productos.created_at','>=', $data['fecha_d'])
                ->whereDate('productos.created_at','<=', $data['fecha_h'])
                ->where('compra_id','>',0)
                ->whereNull('productos.deleted_at')
                ->groupBy(DB::raw('clase,quilates'))
                ->union($union1);

        $data = DB::table( DB::raw("({$union2->toSql()}) temp") )
                ->mergeBindings($union2)
                ->selectRaw('clase,quilates,SUM(importe) AS importe,SUM(peso_gr) AS peso_gr')
                ->groupBy(DB::raw('clase,quilates '.$rollup))
                ->get();


        return $data;

    }

    private function ventas($data){

        $rollup = ($data['totales'] == true) ?  'WITH ROLLUP' : null;

        $select = DB::getTablePrefix().'albaranes.tipo_id AS tipo_id,'.DB::getTablePrefix().'clases.nombre AS clase, SUM('.DB::getTablePrefix().'albalins.importe_venta) AS importe, SUM('.DB::getTablePrefix().'albalins.unidades) AS peso_gr';

        if ($data['facturado'] == "F")
            $union0 = DB::table('albaranes')
            ->select(DB::raw($select))
            ->join('albalins','albaranes.id','=','albalins.albaran_id')
            ->join('productos','productos.id','=','albalins.producto_id')
            ->join('clases','clase_id','=','clases.id')
            ->where('albaranes.empresa_id', session('empresa')->id)
      //      ->where('fase_id','<>', 10)
            ->whereDate('fecha_factura','>=', $data['fecha_d'])
            ->whereDate('fecha_factura','<=', $data['fecha_h'])
            ->whereNull('albalins.deleted_at')
            ->groupBy(DB::raw('tipo_id,clase '.$rollup))
            ->get();
        else
            $union0 = DB::table('albaranes')
                ->select(DB::raw($select))
                ->join('albalins','albaranes.id','=','albalins.albaran_id')
                ->join('productos','productos.id','=','albalins.producto_id')
                ->join('clases','clase_id','=','clases.id')
                ->where('albaranes.empresa_id', session('empresa')->id)
     //           ->where('fase_id','<>', 10)
                ->whereDate('fecha_albaran','>=', $data['fecha_d'])
                ->whereDate('fecha_albaran','<=', $data['fecha_h'])
                ->whereNull('albalins.deleted_at')
                ->groupBy(DB::raw('tipo_id,clase '.$rollup))
                ->get();


        return $union0;
    }

    // private function depositos($data){

    //     $rollup = ($data['totales'] == true) ?  'WITH ROLLUP' : null;

    //      $select = DB::getTablePrefix().'compras.tipo_id AS tipo_id,'.DB::getTablePrefix().'clases.nombre AS clase, 0 AS quilates, SUM('.DB::getTablePrefix().'depositos.importe) AS importe, SUM('.DB::getTablePrefix().'depositos.peso_gr) AS peso_gr';

    //      $union0 = DB::table('compras')
    //          ->select(DB::raw($select))
    //          ->join('depositos','compras.id','=','depositos.compra_id')
    //          ->join('clases','clase_id','=','clases.id')
    //          ->where('compras.empresa_id', session('empresa')->id)
    //          ->whereIn('compras.fase_id', [4,6])
    //         //  ->whereDate('fecha_compra','>=', $data['fecha_d'])
    //          ->whereDate('fecha_compra','<=', $data['fecha_h'])
    //          ->whereNull('fecha_liquidado')
    //          ->groupBy(DB::raw('tipo_id,clase,quilates '.$rollup))
    //          ->get();

    //      return $union0;
    //  }


    private function depositos($data){

        $rollup = ($data['totales'] == true) ?  'WITH ROLLUP' : null;

         $select = DB::getTablePrefix().'compras.tipo_id AS tipo_id,'.DB::getTablePrefix().'clases.nombre AS clase, 0 AS quilates, SUM('.DB::getTablePrefix().'comlines.importe) AS importe, SUM('.DB::getTablePrefix().'comlines.peso_gr) AS peso_gr';

         $union0 = DB::table('compras')
             ->select(DB::raw($select))
             ->join('comlines','compras.id','=','comlines.compra_id')
             ->join('clases','clase_id','=','clases.id')
             ->where('compras.empresa_id', session('empresa')->id)
             ->whereIn('compras.fase_id', [4,6])
            //  ->whereDate('fecha_compra','>=', $data['fecha_d'])
             ->whereDate('fecha_compra','<=', $data['fecha_h'])
             ->whereNull('fecha_liquidado')
             ->groupBy(DB::raw('tipo_id,clase,quilates '.$rollup))
             ->get();

         return $union0;
     }

     private function inventario($data){

        $rollup = ($data['totales'] == true) ?  'WITH ROLLUP' : null;

        $select = DB::getTablePrefix().'clases.nombre AS clase, SUM('.DB::getTablePrefix().'productos.precio_coste) AS importe, SUM('.DB::getTablePrefix().'productos.peso_gr) AS peso_gr';

        $union0 = DB::table('productos')
            ->select(DB::raw($select))
            ->join('clases','clase_id','=','clases.id')
            ->where('productos.empresa_id', session('empresa_id'))
            ->whereIn('estado_id',[1,2,3])
            ->whereDate('productos.created_at','<=', $data['fecha_h'])
            ->whereNull('productos.deleted_at')
            ->groupBy(DB::raw('clase '.$rollup))
            ->get();


        return $union0;
    }



     /**
     * Recibe las facturas por request, previamente de $this->lisfac()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new CuadroMandoExport($request->data_com,
                                                    $request->data_liq,
                                                    $request->data_inv,
                                                    $request->data_net,
                                                    $request->data_ven,
                                                    $request->data_dep,
                                                    $request->data_pro), 'mando.xlsx');

    }

}
