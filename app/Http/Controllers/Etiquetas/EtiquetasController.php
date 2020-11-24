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

        // if (!auth()->user()->hasRole('Gestor')){
        //     return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Gestor');
        // }

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

        $clase_id = $data['clase_id'];

        // Producto::whereIn('estado_id',[1,2,3])
        //             ->fecha($data['fecha_d'],$data['fecha_h'],$data['tipo_fecha'])
        //             ->when($clase_id > 0, function ($query) use ($clase_id) {
        //                 return $query->where('clase_id', $clase_id);})
        //             ->whereIn('etiqueta_id', [2,3,4])
        //             ->whereNull('deleted_at')
        //             ->update(['etiqueta_id' => 5]);

        return Excel::download(new EtiquetasRolloExport($etiquetas), 'Etiquetas.xlsx');

    }

}
