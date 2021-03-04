<?php

namespace App\Http\Controllers\Exportar;

use App\Tipo;
use App\Clase;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use Illuminate\Support\Facades\DB;
use App\Exports\MetalDepositoExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class MetalDepositoController extends Controller
{
    public function index(){


        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        if (request()->wantsJson())
            return [
                'tipos' => Tipo::selTiposCom(),
                //'clases' => Clase::selGrupoClase()
            ];

    }

    public function submit(Request $request){

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $request->validate([
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
            'tipo_id'  => ['required','integer'],
        ]);

        return $this->depositos($data);

    }

    private function depositos($data){

        //$rollup = ($data['totales'] == true) ?  'WITH ROLLUP' : null;
        $rollup = 'WITH ROLLUP';

        $select = DB::getTablePrefix().'empresas.nombre AS empresa,'.DB::getTablePrefix().'clases.nombre AS clase, SUM('.DB::getTablePrefix().'comlines.importe) AS importe, SUM('.DB::getTablePrefix().'comlines.peso_gr) AS peso_gr';

        $union0 = DB::table('compras')
             ->select(DB::raw($select))
             ->join('empresas','empresas.id','=','compras.empresa_id')
             ->join('comlines','compras.id','=','comlines.compra_id')
             ->join('clases','clase_id','=','clases.id')
             ->whereIn('compras.empresa_id', session('empresas_usuario'))
             ->whereIn('compras.fase_id', [4,6])
             ->where('tipo_id', $data['tipo_id'])
             ->whereDate('fecha_compra','>=', $data['fecha_d'])
             ->whereDate('fecha_compra','<=', $data['fecha_h'])
             ->whereNull('fecha_liquidado')
             ->groupBy(DB::raw('empresa,clase '.$rollup))
             ->get();

         return $union0;
     }

    public function excel(Request $request){

        return Excel::download(new MetalDepositoExport($request->data), 'metdep.xlsx');

    }

}
