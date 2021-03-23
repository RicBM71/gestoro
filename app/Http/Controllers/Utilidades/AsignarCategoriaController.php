<?php

namespace App\Http\Controllers\Utilidades;

use App\Producto;
use App\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AsignarCategoriaController extends Controller
{
    public function index(){

        if (!hasEdtPro()){
            return abort(403, ' NO tiene permiso de acceso - Edit Productos.');
        }

        $productos = Producto::whereNull('categoria_id')->whereNull('deleted_at')->whereIn('estado_id',[1,2,3])->get();

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
        ]);

        if (strpos($data['texto'],','))
            $words = explode(",", $data['texto']);
        else
            $words = array($data['texto']);


        $productos = $this->update($data['categoria_id'], $words, $data['reasignar']);

        if (request()->wantsJson())
            return [
                'productos' => $productos->take(200),
                'resto'     => $productos->count()
            ];

    }

    private function update($categoria_id, $words, $reasignar){

        $empresa_id = session('empresa_id');

        foreach ($words as $w){

            $word = strtoupper($w);

            if ($reasignar)
                DB::unprepared('UPDATE klt_productos SET categoria_id = '.$categoria_id.' WHERE (empresa_id = '.$empresa_id.' OR destino_empresa_id = '.$empresa_id.') AND nombre LIKE "%'.$word.'%"');
            else
                DB::unprepared('UPDATE klt_productos SET categoria_id = '.$categoria_id.' WHERE (empresa_id = '.$empresa_id.' OR destino_empresa_id = '.$empresa_id.') AND categoria_id IS NULL AND nombre LIKE "%'.$word.'%"');
        }

        return Producto::whereNull('categoria_id')->whereNull('deleted_at')->whereIn('estado_id',[1,2,3])->get();
    }
}
