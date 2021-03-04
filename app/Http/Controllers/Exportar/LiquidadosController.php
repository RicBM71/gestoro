<?php

namespace App\Http\Controllers\Exportar;

use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use App\Exports\LiquidadosExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class LiquidadosController extends Controller
{
    public function liquidados(Request $request){

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $request->validate([
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
        ]);

        return $this->detalle($data);

    }


    private function detalle($data){



        $select=DB::getTablePrefix().'empresas.nombre AS empresa,'.
                DB::getTablePrefix().'clases.nombre AS clase, '.
                DB::getTablePrefix().'comlines.quilates AS quilates,'.
                'SUM('.DB::getTablePrefix().'comlines.importe) AS importe,'.
                'SUM('.DB::getTablePrefix().'comlines.peso_gr) AS peso_gr';

        $union1 = DB::table('compras')
            ->select(DB::raw($select))
                ->join('empresas','compras.empresa_id','=','empresas.id')
                ->join('comlines','compras.id','=','compra_id')
                ->join('clases','comlines.clase_id','=','clases.id')
                ->where('compras.empresa_id', session('empresa')->id)
                //->whereIn('compras.empresa_id', session('empresas_usuario'))
                ->whereDate('fecha_liquidado','>=', $data['fecha_d'])
                ->whereDate('fecha_liquidado','<=', $data['fecha_h'])
                ->groupBy(DB::raw('empresa,clase,quilates'));

        $select=DB::getTablePrefix().'empresas.nombre AS empresa,'.
                DB::getTablePrefix().'clases.nombre AS clase, '.
                DB::getTablePrefix().'productos.quilates AS quilates,'.
                'SUM('.DB::getTablePrefix().'productos.precio_coste * -1) AS importe,'.
                'SUM('.DB::getTablePrefix().'productos.peso_gr * -1) AS peso_gr';


        $union2 = DB::table('productos')
            ->select(DB::raw($select))
                ->join('empresas','productos.empresa_id','=','empresas.id')
                ->join('clases','productos.clase_id','=','clases.id')
                ->where('productos.empresa_id', session('empresa')->id)
            //    ->whereIn('productos.empresa_id', session('empresas_usuario'))
                ->whereDate('productos.created_at','>=', $data['fecha_d'])
                ->whereDate('productos.created_at','<=', $data['fecha_h'])
                ->where('compra_id','>',0)
                ->whereNull('productos.deleted_at')
                ->groupBy(DB::raw('empresa,clase,quilates'))
                ->union($union1);

        $data = DB::table( DB::raw("({$union2->toSql()}) temp") )
                ->mergeBindings($union2)
                ->selectRaw('empresa,clase,quilates,SUM(importe) AS importe,SUM(peso_gr) AS peso_gr')
                ->groupBy(DB::raw('empresa,clase,quilates WITH ROLLUP'))
                ->get();


        return $data;

    }

    /**
     * Recibe las facturas por request, previamente de $this->lisfac()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new LiquidadosExport($request->data, 'Liquidados'), 'liquidados.xlsx');

    }

}
