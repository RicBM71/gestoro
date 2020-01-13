<?php

namespace App\Http\Controllers\Exportar;

use App\Producto;
use Illuminate\Http\Request;
use App\Exports\InventarioExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class InventarioController extends Controller
{

    public function inventario(Request $request){

        if (!auth()->user()->hasRole('Gestor')){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Gestor');
        }

        $data = $request->validate([
            'cliente_id'  => ['required','integer'],
            'clase_id'  => ['required','integer'],
        ]);

        return $this->detalle($data);

    }

    private function detalle($data)
    {

        $data = Producto::with(['clase','iva','estado','garantia','cliente'])
                    ->asociado($data['cliente_id'])
                    ->clase($data['clase_id'])
                    ->whereIn('estado_id',[1,2,3])
                    ->get();

        if (request()->wantsJson())
            return [
                'inventario' => $data,
                'valor_inventario' => $data->sum('precio_coste')
            ];


    }

     /**
     * Recibe las facturas por request, previamente de $this->lisfac()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new InventarioExport($request->data, 'Inventario '.session('empresa')->razon), 'inventario.xlsx');


    }


}
