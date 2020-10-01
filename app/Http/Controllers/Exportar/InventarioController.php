<?php

namespace App\Http\Controllers\Exportar;

use App\Producto;
use Illuminate\Http\Request;
use App\Exports\InventarioExport;
use Illuminate\Support\Facades\DB;
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
                        //->select('productos.*',)
                        ->select(DB::raw(DB::getTablePrefix().'productos.*, (stock - (IFNULL((SELECT SUM(unidades) FROM '.DB::getTablePrefix().'albalins,'.DB::getTablePrefix().'albaranes WHERE producto_id = '.DB::getTablePrefix().'productos.id and '.DB::getTablePrefix().'albalins.deleted_at is null AND albaran_id = '.DB::getTablePrefix().'albaranes.id AND fase_id = 3), 0))) AS mi_stock'))
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
                        ->select(DB::raw(DB::getTablePrefix().'productos.*, (stock - (IFNULL((SELECT SUM(unidades) FROM '.DB::getTablePrefix().'albalins,'.DB::getTablePrefix().'albaranes WHERE producto_id = '.DB::getTablePrefix().'productos.id and '.DB::getTablePrefix().'albalins.deleted_at is null AND albaran_id = '.DB::getTablePrefix().'albaranes.id AND fase_id = 3), 0))) AS mi_stock'))
                        ->join('clases','clase_id','=','clases.id')
                        ->where('destino_empresa_id', session('empresa_id'))
                        ->asociado($data['cliente_id'])
                        ->estado($data['estado_id'])
                        ->clase($data['clase_id'])
                        ->whereIn('estado_id',[1,2,3])
                        ->grupo($data['grupo_id'])
                        ->get();

        $valor_inventario = 0;
        foreach ($data as $row){
            $valor_inventario += round($row->mi_stock * $row->precio_coste, 2);
        }


        if (request()->wantsJson())
            return [
                'inventario' => $data,
                'valor_inventario' => $valor_inventario
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
