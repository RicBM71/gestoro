<?php

namespace App\Http\Controllers\Etiquetas;

use App\Clase;
use App\Etiqueta;
use App\Producto;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use App\Http\Controllers\Controller;
use App\Rules\MaxDiasRangoFechaRule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EtiquetasRolloExport;

class EtiquetasController extends Controller
{
    public function index(){


        if (request()->wantsJson())
            return [
                'clases'    => Clase::selGrupoClase(),
                'etiquetas' => Etiqueta::selImprimibles(),
            ];

    }


    public function submit(Request $request){

        $data = $request->validate([
            'clase_id'     => ['nullable','integer'],
            'tipo_fecha'    =>['string','required'],
            'fecha_d'       =>['nullable','date',new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'       =>['nullable','date',new MaxDiasRangoFechaRule($request->fecha_d, $request->fecha_h)],
        ]);

        $etiquetas = Producto::with('clase')
                        ->fecha($data['fecha_d'],$data['fecha_h'],$data['tipo_fecha'])
                        ->clase($data['clase_id'])
                        ->whereIn('estado_id',[1,2,3])
                        ->whereIn('etiqueta_id', [2,3,4])
                        ->whereNull('deleted_at')
                        ->orderBy('referencia')
                        ->get();

        if ($etiquetas->count() == 0)
            return abort(404, 'No hay etiquetas!');

        $collection = collect();
        foreach ($etiquetas as $producto){

            if ($producto->clase_id > 1 && $producto->precio_venta == 0)
                continue;

            $collection->push($producto);

            $producto->update(['etiqueta_id' => 5]);

        }


        return Excel::download(new EtiquetasRolloExport($collection), 'Etiquetas.xlsx');

    }

}
