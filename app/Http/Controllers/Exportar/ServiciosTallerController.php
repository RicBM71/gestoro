<?php

namespace App\Http\Controllers\Exportar;

use App\Clase;
use App\Taller;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServiciosTallerExport;

class ServiciosTallerController extends Controller
{
    public function index(){

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        if (request()->wantsJson())
            return [
                'talleres'=> Taller::selTalleres(),
            ];

    }

    public function submit(Request $request){

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $request->validate([
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
            'operacion'=> ['required','string'],
            'taller_id'=> ['required','integer'],
        ]);

        return $this->detalle($data);

    }

    private function detalle($data){

        if ($data['operacion'] == 'F')
            $where = 'factura > ""';
        elseif ($data['operacion'] == 'N')
            $where = DB::getTablePrefix().'albaranes.factura IS NULL';
        else
            $where = DB::getTablePrefix().'albaranes.id > 0';


        $select=DB::getTablePrefix().'albaranes.id AS id, fecha_albaran AS fecha,albaran,serie_albaran AS serie, notas_ext,'.
                DB::getTablePrefix().'productos.nombre AS producto,'.
                'SUM('.DB::getTablePrefix().'albalins.precio_coste) AS precio_coste';

        $result = DB::table('albaranes')
                        ->select(DB::raw($select))
                        ->join('albalins','albaranes.id','=','albalins.albaran_id')
                        ->join('productos','productos.id','=','albalins.producto_id')
                        ->where('albaranes.empresa_id', session('empresa')->id)
            //            ->where('fase_id', '<>', 10)
                        ->whereNull('albaranes.deleted_at')
                        ->whereDate('albaranes.fecha_albaran','>=', $data['fecha_d'])
                        ->whereDate('albaranes.fecha_albaran','<=', $data['fecha_h'])
                        ->where('tipo_id', 5)
                        ->whereRaw($where)
                        ->groupBy(DB::raw('id, fecha_albaran,albaran,serie_albaran,notas_ext,producto'))
                        ->get();

        return $result;

    }

     /**
     * Recibe las facturas por request, previamente de $this->lisfac()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new ServiciosTallerExport($request->data, 'Taller '.$request->titulo), 'file.xlsx');

    }
}
