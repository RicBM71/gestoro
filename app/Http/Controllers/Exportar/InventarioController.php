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

        if (!auth()->user()->hasPermissionTo('consultas')){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $request->validate([
            'cliente_id'   => ['nullable','integer'],
            'clase_id'     => ['nullable','integer'],
            'estado_id'    => ['nullable','integer'],
            'marca_id'     => ['nullable','integer'],
            'categoria_id' => ['nullable','integer'],
            'tipoinv_id'   => ['required','max:1'],
            'created_at'   => ['nullable','date'],
        ]);

        return $this->detalle($data);

    }

    private function detalle($data)
    {

        $created_at = $data['created_at'];

        // \Log::info(Producto::withOutGlobalScope(EmpresaProductoScope::class)->with(['clase','iva','estado','garantia','cliente','etiqueta'])
        //                 //->select('productos.*',)
        //                 ->select(DB::raw(DB::getTablePrefix().'productos.*, (stock - (IFNULL((SELECT SUM(unidades) FROM '.DB::getTablePrefix().'albalins,'.DB::getTablePrefix().'albaranes WHERE producto_id = '.DB::getTablePrefix().'productos.id and '.DB::getTablePrefix().'albalins.deleted_at is null AND albaran_id = '.DB::getTablePrefix().'albaranes.id AND fase_id >= 10), 0))) AS mi_stock'))
        //                 ->join('clases','clase_id','=','clases.id')
        //                 ->where('empresa_id', session('empresa_id'))
        //                 ->categoria($data['categoria_id'])
        //                 ->marca($data['marca_id'])
        //                 ->asociado($data['cliente_id'])
        //                 ->estado($data['estado_id'])
        //                 ->clase($data['clase_id'])
        //                 ->whereIn('estado_id',[1,2,3])
        //                 ->grupo($data['grupo_id'])
        //                 ->when($created_at <> null, function ($query) use ($created_at) {
        //                     return $query->where('productos.created_at', '<=', $created_at);
        //                 })->toSql());

        // solo lístamos los productos de empresa origen porque este es el inventario real
        //  por esto quito globalScope
        // if ($data['tipoinv_id'] == 'C')
        //     $data = Producto::withOutGlobalScope(EmpresaProductoScope::class)->with(['clase','iva','estado','garantia','cliente','etiqueta'])
        //                 ->select(DB::raw(DB::getTablePrefix().'productos.*, (stock - (IFNULL((SELECT SUM(unidades) FROM '.DB::getTablePrefix().'albalins,'.DB::getTablePrefix().'albaranes WHERE producto_id = '.DB::getTablePrefix().'productos.id and '.DB::getTablePrefix().'albalins.deleted_at is null AND albaran_id = '.DB::getTablePrefix().'albaranes.id AND fase_id >= 10), 0))) AS mi_stock'))
        //                 ->join('clases','clase_id','=','clases.id')
        //                 ->where('empresa_id', session('empresa_id'))
        //                 ->categoria($data['categoria_id'])
        //                 ->marca($data['marca_id'])
        //                 ->asociado($data['cliente_id'])
        //                 ->estado($data['estado_id'])
        //                 ->clase($data['clase_id'])
        //                 ->whereIn('estado_id',[1,2,3])
        //                 ->grupo($data['grupo_id'])
        //                 ->when($created_at <> null, function ($query) use ($created_at) {
        //                     return $query->where('productos.created_at', '<=', $created_at);
        //                 })
        //                 ->get();
        // else
        //     $data = Producto::withOutGlobalScope(EmpresaProductoScope::class)->with(['clase','iva','estado','garantia','cliente','etiqueta'])
        //                 ->select(DB::raw(DB::getTablePrefix().'productos.*, (stock - (IFNULL((SELECT SUM(unidades) FROM '.DB::getTablePrefix().'albalins,'.DB::getTablePrefix().'albaranes WHERE producto_id = '.DB::getTablePrefix().'productos.id and '.DB::getTablePrefix().'albalins.deleted_at is null AND albaran_id = '.DB::getTablePrefix().'albaranes.id AND fase_id >= 10), 0))) AS mi_stock'))
        //                 ->join('clases','clase_id','=','clases.id')
        //                 ->where('destino_empresa_id', session('empresa_id'))
        //                 ->categoria($data['categoria_id'])
        //                 ->marca($data['marca_id'])
        //                 ->asociado($data['cliente_id'])
        //                 ->estado($data['estado_id'])
        //                 ->clase($data['clase_id'])
        //                 ->whereIn('estado_id',[1,2,3])
        //                 ->grupo($data['grupo_id'])
        //                 ->when($created_at <> null, function ($query) use ($created_at) {
        //                     return $query->where('productos.created_at', '<=', $created_at);
        //                 })
        //                 ->get();

        if ($data['tipoinv_id'] == 'C')
            $data = Producto::withOutGlobalScope(EmpresaProductoScope::class)->with(['clase','estado','cliente','etiqueta'])
                        ->where('empresa_id', session('empresa_id'))
                        ->categoria($data['categoria_id'])
                        ->marca($data['marca_id'])
                        ->asociado($data['cliente_id'])
                        ->estado($data['estado_id'])
                        ->clase($data['clase_id'])
                        ->whereIn('estado_id',[1,2,3])
                        //->where('stock','>',1)
                        ->when($created_at <> null, function ($query) use ($created_at) {
                            return $query->where('productos.created_at', '<=', $created_at);
                        })
                        ->get();
        else
            $data = Producto::withOutGlobalScope(EmpresaProductoScope::class)->with(['clase','estado','cliente','etiqueta'])
                        ->where('destino_empresa_id', session('empresa_id'))
                        ->categoria($data['categoria_id'])
                        ->marca($data['marca_id'])
                        ->asociado($data['cliente_id'])
                        ->estado($data['estado_id'])
                        ->clase($data['clase_id'])
                        ->whereIn('estado_id',[1,2,3])
                        ->when($created_at <> null, function ($query) use ($created_at) {
                            return $query->where('productos.created_at', '<=', $created_at);
                        })
                        ->get();

        $inventario = array();
        $valor_inventario = 0;
        foreach ($data as $row){



            if ($row->stock > 1){

                $vendidas = DB::table('albalins')
                                    ->join('albaranes','albaran_id','=','albaranes.id')
                                    ->where('producto_id', $row->id)
                                    ->where('fase_id','>=',10)
                                    ->whereNull('albalins.deleted_at')
                                    ->get()->sum('unidades');

                $stock = $row->stock - $vendidas;

                if ($stock == 0)
                    continue;

                $row->stock = $stock;

                $valor_inventario += round($stock * $row->precio_coste, 2);
            }
            else
                $valor_inventario += $row->precio_coste;

            $inventario[] = $row;

        }


        if (request()->wantsJson())
            return [
                'inventario' => $inventario,
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

        $items = $request->validate(['data'=>'array']);

        return Excel::download(new InventarioExport($items['data'], 'Inventario '.session('empresa')->razon), 'inventario.xlsx');


    }


}
