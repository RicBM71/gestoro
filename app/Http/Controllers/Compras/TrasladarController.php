<?php

namespace App\Http\Controllers\Compras;

use App\Libro;
use App\Compra;
use App\Empresa;
use Carbon\Carbon;
use App\Traits\SessionTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TrasladarController extends Controller
{
    use SessionTrait;

    public function index(){

        if (request()->wantsJson())
            return [
                'empresas' => Empresa::selEmpresas()->flag(3)->where('id','<>',session('empresa_id'))->whereIn('id',session('empresas_usuario'))->get()
            ];

    }

    public function update(Request $request, Compra $compra){

        $libro = Libro::restaContadorCompra($compra->ejercicio, $compra->grupo_id, $compra->albaran);

        $parametros = $this->loadSession($request->destino_empresa_id);

        $contador = Libro::incrementaContador($compra->ejercicio, $compra->grupo_id, null);


        $data = [
            'empresa_id' => $request->destino_empresa_id,
            'username'   => session('username'),
            'albaran'    => $contador['albaran'],
            'updated_at' => Carbon::now()
        ];

        DB::table('compras')->where('id', $compra->id)->update($data);
        DB::table('comlines')->where('compra_id', $compra->id)->update(['empresa_id' => $request->destino_empresa_id]);
        DB::table('depositos')->where('compra_id', $compra->id)->update(['empresa_id' => $request->destino_empresa_id]);

        $compra->load(['depositos']);

        foreach ($compra->depositos as $deposito) {

            $concepto = "COMPRA ".$compra->serie_com." ".$contador['albaran'].' DEPOSITO EFECTIVO -TRASLADO-';

            DB::table('cajas')->where('deposito_id', $deposito->id)->update(['empresa_id' => $request->destino_empresa_id,'nombre' => $concepto]);
        }

        return $libro;

    }
}
