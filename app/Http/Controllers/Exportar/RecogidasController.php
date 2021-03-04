<?php

namespace App\Http\Controllers\Exportar;

use App\Clase;
use App\Taller;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RecogidasExport;

class RecogidasController extends Controller
{
    public function index(){



    }

    public function submit(Request $request){

        $data = $request->validate([
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
        ]);

        return $this->detalle($data);

    }

    private function detalle($data){

        $result = DB::table('compras')
                        ->select('compras.*','empresas.nombre AS empresa','clientes.razon')
                        ->join('empresas','empresas.id','=','empresa_id')
                        ->join('clientes','clientes.id','=','cliente_id')
                        ->whereIn('compras.empresa_id', session('empresas_usuario'))
                        ->where('tipo_id', 1)
                        ->whereIn('fase_id', [4,5])
                        ->whereDate('compras.fecha_recogida','>=', $data['fecha_d'])
                        ->whereDate('compras.fecha_recogida','<=', $data['fecha_h'])
                        ->whereNull('compras.deleted_at')
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

        return Excel::download(new RecogidasExport($request->data, 'Recogidas'), 'file.xlsx');

    }
}
