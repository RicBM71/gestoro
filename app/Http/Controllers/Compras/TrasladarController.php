<?php

namespace App\Http\Controllers\Compras;

use App\Cruce;
use App\Libro;
use App\Compra;
use App\Empresa;
use Carbon\Carbon;
use App\Scopes\EmpresaScope;
use App\Traits\SessionTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TrasladarController extends Controller
{
    use SessionTrait;

    public function index(){

        $empresas = Empresa::selEmpresas()->flag(3)->where('id','<>',session('empresa_id'))->whereIn('id',session('empresas_usuario'))->get();

        // $empresa_id = $empresas[0]->value;

        // Libro::selLibrosByEjercicio(Carbon::today()->format('Y'));

        if (request()->wantsJson())
            return [
                'empresas' => $empresas,
            ];

    }

    public function grupo($empresa_id){


        $grupos = Libro::selLibrosGrupoEmpresa($empresa_id, date('Y'));

        if (request()->wantsJson())
            return [
                'grupos' => $grupos,
            ];

    }

    public function update(Request $request, Compra $compra){

        $data = $request->validate([
            'destino_empresa_id' => 'required|integer',
            'destino_grupo_id' => 'required|integer',
        ]);


        if (Carbon::parse($compra->fecha_compra) != Carbon::today()){
            return abort(403, 'Solo pueden trasladarse si fecha compra es igual a hoy. Contactar admin');
        }


        $libro = Libro::restaContadorCompra($compra->ejercicio, $compra->grupo_id, $compra->albaran, 0);
        // $contador = Libro::where('grupo_id',$grupo_id)
        //     ->where('ejercicio',$ejercicio)
        //     ->lockForUpdate()->firstOrFail();

        $parametros = $this->loadSession($data['destino_empresa_id']);

        $contador = Libro::incrementaContador($compra->ejercicio, $data['destino_grupo_id'], null);

        $data_new = [
            'empresa_id' => $data['destino_empresa_id'],
            'grupo_id'   => $data['destino_grupo_id'],
            'serie_com'  => $contador['serie_com'],
            'dias_custodia' => $contador['dias_custodia'],
            'interes'       => $contador['interes'],
            'fecha_bloqueo' => Compra::Bloqueo($compra->fecha_compra, $contador['semdia_bloqueo']),
            'username'   => session('username'),
            'albaran'    => $contador['albaran'],
            'updated_at' => Carbon::now()
        ];

        if ($compra->tipo_id==1){
            $data_new['fecha_renovacion'] = $compra->fecha_compra->addDays($contador['dias_custodia']);
        }else{
            $data_new['fecha_renovacion'] = null;
        }

        DB::table('compras')->where('id', $compra->id)->update($data_new);
        DB::table('comlines')->where('compra_id', $compra->id)->update(['empresa_id' => $data['destino_empresa_id']]);
        DB::table('depositos')->where('compra_id', $compra->id)->update(['empresa_id' => $data['destino_empresa_id']]);

        $compra->load(['depositos']);

        try {

            $cruce = Cruce::withOutGlobalScope(EmpresaScope::class)->where('empresa_id',$data['destino_empresa_id'])
                                ->where('comven', 'C')
                                ->firstOrFail();

            $destino_caja_empresa_id = $cruce->destino_empresa_id;
        } catch (\Exception $e) {
            $destino_caja_empresa_id = $data['destino_empresa_id'];
        }


        foreach ($compra->depositos as $deposito) {

            $concepto = "COMPRA ".$compra->serie_com." ".$contador['albaran'].' DEPOSITO EFECTIVO -TRASLADO-';

            DB::table('cajas')->where('deposito_id', $deposito->id)->update(['empresa_id' => $destino_caja_empresa_id,'nombre' => $concepto]);
        }

        return $libro;

    }
}
