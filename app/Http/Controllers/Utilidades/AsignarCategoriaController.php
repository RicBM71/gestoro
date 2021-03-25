<?php

namespace App\Http\Controllers\Utilidades;

use App\Producto;
use App\Categoria;
use Carbon\Carbon;
use App\Scopes\EmpresaProductoScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AsignarCategoriaController extends Controller
{
    public function index(){

        if (!hasEdtPro()){
            return abort(403, ' NO tiene permiso de acceso - Edit Productos.');
        }

        $productos = Producto::withoutGlobalScope(EmpresaProductoScope::class)
                            ->whereIn('empresa_id', session('empresas_usuario'))
                            ->whereNull('categoria_id')
                            ->whereNull('deleted_at')
                            ->whereIn('estado_id',[1,2,3])->get();



        if (request()->wantsJson())
            return [
                'categorias' => Categoria::selCategorias(),
                'productos'  => $productos->take(200),
                'resto'      => $productos->count()
            ];


    }

    public function submit(Request $request)
    {

        if (!hasEdtPro()){
            return abort(403, ' NO tiene permiso de acceso - Edit Productos.');
        }

        $data = $request->validate([
            'categoria_id'  => ['required','integer'],
            'texto'         => ['required','max:190'],
            'reasignar'     => ['required','boolean'],
            'filtrar'       => ['required','boolean'],
        ]);

        if (strpos($data['texto'],','))
            $words = explode(",", $data['texto']);
        else
            $words = array($data['texto']);


        $productos = $this->update($data['categoria_id'], $words, $data['reasignar'], $data['filtrar']);

        if (request()->wantsJson())
            return [
                'productos' => $productos->take(200),
                'resto'     => $productos->count()
            ];

    }

    private function update($categoria_id, $words, $reasignar,$filtrar){

        $dt = Carbon::now();

        $empresa_id = session('empresa_id');

        $empresas = session('empresas_usuario')->implode(',');

        foreach ($words as $w){

            if ($w == '' || $w == null)
                continue;

            $word = strtoupper($w);

            if ($reasignar)
                DB::unprepared('UPDATE klt_productos SET categoria_id = '.$categoria_id.', updated_at ="'.$dt.'" WHERE empresa_id IN ('.$empresas.') AND nombre LIKE "%'.$word.'%" AND estado_id IN (1,2,3)');
            else
                if ($filtrar)
                    DB::unprepared('UPDATE klt_productos SET categoria_id = '.$categoria_id.', updated_at ="'.$dt.'" WHERE empresa_id IN ('.$empresas.') AND categoria_id ='.$categoria_id.' AND nombre LIKE "%'.$word.'%"  AND estado_id IN (1,2,3,4)');
                else
                    DB::unprepared('UPDATE klt_productos SET categoria_id = '.$categoria_id.', updated_at ="'.$dt.'" WHERE empresa_id IN ('.$empresas.') AND categoria_id IS NULL AND nombre LIKE "%'.$word.'%" AND estado_id IN (1,2,3)');
        }

        if ($filtrar)
            return Producto::withoutGlobalScope(EmpresaProductoScope::class)->whereIn('empresa_id', session('empresas_usuario'))->where('categoria_id', $categoria_id)->whereNull('deleted_at')->whereIn('estado_id',[1,2,3])->get();
        else
            return Producto::withoutGlobalScope(EmpresaProductoScope::class)->whereIn('empresa_id', session('empresas_usuario'))->whereNull('categoria_id')->whereNull('deleted_at')->whereIn('estado_id',[1,2,3])->get();
    }
}
