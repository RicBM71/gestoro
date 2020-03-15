<?php

namespace App\Http\Controllers\Utilidades;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ImportarProductoController extends Controller
{
     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (!esRoot())
            return abort(411, 'Root requerido');

        $data = $request->validate([
                    'referencia'=>['required','integer'],
                    'prefijo'=>['nullable','max:3']
                ]);

        // leo de kilates, origen
        if ($data['prefijo'] == null)
            $productos = DB::connection('db2')->select('select * from klt_productos WHERE id = ?',[$data['referencia']]);
        else{

            $referencia = strtoupper($data['prefijo']).$data['referencia'];
            $productos = DB::connection('db2')->select('select * from klt_productos WHERE referencia = ?', [$referencia]);

        }

        $existen = $creados = 0 ;

        foreach ($productos as $producto){

            $pro_rj = DB::table('productos')->where('referencia', $producto->referencia);

            if ($pro_rj->exists()){
                $existen++;
                continue;
            }

            $pro_rj2 = $pro_rj->first();

            $this->crearProducto($producto);
            $creados++;

        }



        if (request()->wantsJson())
            if ($creados > 0)
                return [
                    'message' => 'EL producto ha sido creado correctamente'
                ];
            else
                return abort(404, 'No se ha encontrado '.$data['referencia']);
    }

    private function crearProducto($producto_kil){

        // foreach ($producto_kil as $d){

            $data = collect($producto_kil)->toArray();

            //$data['id']=null;
            $data['empresa_id']=session('empresa_id');
            $data['destino_empresa_id']=session('empresa_id');
            $data['created_at']=Carbon::now();
            $data['updated_at']=Carbon::now();
            $data['username']='importado';


        return DB::table('productos')->insertGetId($data);

    }
}
