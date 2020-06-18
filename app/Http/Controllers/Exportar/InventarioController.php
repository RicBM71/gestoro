<?php

namespace App\Http\Controllers\Exportar;

use App\Producto;
use Illuminate\Http\Request;
use App\Exports\InventarioExport;
use App\Http\Controllers\Controller;
use App\Scopes\EmpresaProductoScope;
use Maatwebsite\Excel\Facades\Excel;

class InventarioController extends Controller
{

    public function inventario(Request $request){

        if (!auth()->user()->hasPermissionTo('edtpro')){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Edit Productos');
        }

        $data = $request->validate([
            'cliente_id'  => ['nullable','integer'],
            'clase_id'    => ['nullable','integer'],
            'estado_id'   => ['nullable','integer'],
            'grupo_id'    => ['required','integer'],
            'tipoinv_id'  => ['required','max:1'],
        ]);

        return $this->detalle($data);

    }

    private function detalle($data)
    {

        // solo lístamos los productos de empresa origen porque este es el inventario real
        //  por esto quito globalScope
        if ($data['tipoinv_id'] == 'C')
            $data = Producto::withOutGlobalScope(EmpresaProductoScope::class)->with(['clase','iva','estado','garantia','cliente','etiqueta'])
                        ->select('productos.*')
                        ->join('clases','clase_id','=','clases.id')
                        ->where('empresa_id', session('empresa_id'))
                        ->asociado($data['cliente_id'])
                        ->estado($data['estado_id'])
                        ->clase($data['clase_id'])
                        ->whereIn('estado_id',[1,2,3])
                        ->grupo($data['grupo_id'])
                        ->get();
        else
            $data = Producto::withOutGlobalScope(EmpresaProductoScope::class)->with(['clase','iva','estado','garantia','cliente','etiqueta'])
                        ->select('productos.*')
                        ->join('clases','clase_id','=','clases.id')
                        ->where('destino_empresa_id', session('empresa_id'))
                        ->asociado($data['cliente_id'])
                        ->estado($data['estado_id'])
                        ->clase($data['clase_id'])
                        ->whereIn('estado_id',[1,2,3])
                        ->grupo($data['grupo_id'])
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
