<?php

namespace App\Http\Controllers\Ventas;

use App\Cobro;
use App\Albaran;
use App\Contador;
use Carbon\Carbon;
use App\Scopes\EmpresaScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ReubicarAlbaranesController extends Controller
{

    protected $contador=0;
    protected $nueva_fecha_albaran = false;

    public function reubicar(Request $request){

        $data = $request->validate([
            'fecha_h' => ['required','date']
        ]);

        if (!esRoot())
            return abort(403,'Solo ROOT hasta verificar!');

        if (!esAdmin())
            return abort(403,'Solo administradores pueden reubicar albaranes!');


        $albaranes = DB::table('albaranes')
                    ->select(DB::raw('DISTINCT '.DB::getTablePrefix().'albaranes.id AS id'))
                    ->join('albalins', 'albaranes.id', '=', 'albalins.albaran_id')
                    ->join('productos', 'albalins.producto_id', '=', 'productos.id')
                    ->where('albaranes.empresa_id', session('empresa')->id)
                    ->where('albaranes.tipo_id', 3)
                    ->whereYear('fecha_albaran', getEjercicio($data['fecha_h']))
                    ->whereDate('fecha_albaran','<=', $data['fecha_h'])
                    ->where('fase_id', 11)
                    ->whereNull('factura')
                    ->whereNull('albaranes.deleted_at')
                    ->where('productos.empresa_id','<>',session('empresa')->id)
                    ->get();

        $this->nueva_fecha_albaran = $data['fecha_h'];

        foreach ($albaranes as $albaran){
            $this->update($albaran->id);
        }

        if (request()->wantsJson())
            return $this->contador;

    }

    public function update($id)
    {
        if (!auth()->user()->hasRole('Admin'))
            return abort(403,'Solo administradores pueden reubicar albaranes!');


        $albaran = Albaran::with(['cliente','albalins.producto'])->findOrFail($id);
       // dd($albaran);

        if ($albaran->tipo_id != 3 || $albaran->factura > 0 || $albaran->fase_id != 11)
            return abort(404,'El albarán no se puede reubicar');


        $collection = $albaran->albalins->sortBy('producto.empresa_id');

        $hay_productos_para_reubicar = false;
        foreach ($collection as $albalin){
            if ($albalin->producto->empresa_id != $albalin->producto->destino_empresa_id || $albalin->producto->cliente_id > 0){
                $hay_productos_para_reubicar = true;
            }
        }

        if (!$hay_productos_para_reubicar){
            return abort(404,'No hay productos para reubicar en el albarán!');
        }

        $empresa_ant = -1;

        $total_albaran = 0;
        $nuevo_albaran = false;

        // primero reubicamos los propios
        foreach ($collection as $albalin){

            //if ($albalin->producto->compra_id == 0 || $albalin->producto->compra_id == null || $albalin->producto->compra_id == "")
            if (!($albalin->producto->compra_id > 0))
                continue;

            if ($empresa_ant != $albalin->producto->empresa_id){
                if ($total_albaran != 0){
                    $this->crearCobro($nuevo_albaran, $albaran->id, $total_albaran);
                }
                $nuevo_albaran = $this->crearAlbaran($albaran, $albalin->producto->empresa_id);
                $empresa_ant = $albalin->producto->empresa_id;
                $total_albaran = 0;
            }

            $this->crearAlbalin($albalin, $nuevo_albaran->id, $empresa_ant);
            $total_albaran+= $albalin->importe_venta;

        }

        if ($total_albaran != 0){
            $this->crearCobro($nuevo_albaran, $albaran->id, $total_albaran);
        }

        $total_albaran = 0;
        $nuevo_albaran_proveedores = false;
       // $empresa_id_proveedores_depo = session('empresa')->deposito_empresa_id;
        $cliente_ant = -1;


        // reubicamos los que son de proveedores
        foreach ($collection as $albalin){

            if ($albalin->producto->compra_id > 0)
                continue;

            if ($cliente_ant != $albalin->producto->cliente_id){
                if ($total_albaran != 0){
                    $this->crearCobro($nuevo_albaran_proveedores, $albaran->id, $total_albaran);
                }

                // siempre acaba albarán en empresa origen
                $empresa_id_proveedores_depo = $albalin->producto->empresa_id;

                $nuevo_albaran_proveedores = $this->crearAlbaran($albaran, $empresa_id_proveedores_depo);

                if ($albalin->producto->cliente_id == null)
                    $cliente_ant = -1;
                else
                    $cliente_ant = $albalin->producto->cliente_id;

                $total_albaran = 0;
            }

            $this->crearAlbalin($albalin, $nuevo_albaran_proveedores->id, $nuevo_albaran_proveedores->empresa_id);

            // if ($albalin->producto->cliente_id > 0)
            //     DB::table('productos')->where('id', $albalin->producto_id)
            //                             ->update([
            //                                 'empresa_id' => $empresa_id_proveedores_depo,
            //                                'destino_empresa_id' => $empresa_id_proveedores_depo
            //                             ]);

            $total_albaran+= $albalin->importe_venta;

        }

        if ($total_albaran != 0){
            $this->crearCobro($nuevo_albaran_proveedores, $albaran->id, $total_albaran);
        }

        // DB::table('cobros')->where('albaran_id', $albaran->id)->delete();
        // DB::table('albalins')->where('albaran_id', $albaran->id)->delete();
        // DB::table('albaranes')->where('id', $albaran->id)->delete();

        if ($nuevo_albaran !== false  || $nuevo_albaran_proveedores !== false){
            DB::table('cobros')->where('albaran_id', $albaran->id)
                ->update(['deleted_at'  => Carbon::now(), 'username'=>'reu.'.session('username')]);
            DB::table('albalins')->where('albaran_id', $albaran->id)
                ->update(['deleted_at'  => Carbon::now(), 'username'=>'reu.'.session('username')]);
            DB::table('albaranes')->where('id', $albaran->id)
                ->update(['deleted_at'  => Carbon::now(), 'username'=>'reu.'.session('username')]);

            //return $nuevo_albaran_proveedores;
        }


        //\Log::info($nuevo_albaran);
        //return abort(404,'No se ha reubicado ningún albarán');

        //return false;


    }

    private function crearAlbaran($albaran, $nueva_empresa_id){

        $data_new['empresa_id']     = $nueva_empresa_id;

        if ($nueva_empresa_id != $albaran->empresa_id)
            $data_new['procedencia_empresa_id'] =  $albaran->empresa_id;

        $data_new['tipo_id']        = $albaran->tipo_id;
        if ($this->nueva_fecha_albaran === false)
            $this->nueva_fecha_albaran = date('Y-m-d');

        $data_new['fecha_albaran']  = $this->nueva_fecha_albaran;
        $data_new['cliente_id']     = $albaran->cliente_id;

        $data_new['fase_id']          = $albaran->fase_id;
        $data_new['online']           = $albaran->online;
        $data_new['iva_no_residente'] = $albaran->iva_no_residente;
        $data_new['username']         = session('username');

        $ejercicio   = getEjercicio($albaran->fecha_albaran);
        $contador_alb = Contador::incrementaContadorReubicar($ejercicio, $albaran->tipo_id, $nueva_empresa_id);
        $data_new['serie_albaran']    = $contador_alb['serie_albaran'];
        $data_new['albaran']      = $contador_alb['ult_albaran'];
        $data_new['notas_int']    = $albaran->notas_int;
        $data_new['notas_ext']    = $albaran->notas_ext;
        $data_new['created_at']   = Carbon::now();
        $data_new['updated_at']   = Carbon::now();

        // lo hago así por no andar mareando con estados según voy insertando, será más rápido

        $id = DB::table('albaranes')->insertGetId($data_new);

        $this->contador++;

        return Albaran::withoutGlobalScope(EmpresaScope::class)->find($id);
    }

    private function crearAlbalin($albalin, $albaran_id_new, $empresa_id_new){

        $albalin_new['albaran_id']      = $albaran_id_new;
        $albalin_new['empresa_id']      = $empresa_id_new;
        $albalin_new['producto_id']     = $albalin->producto_id;
        $albalin_new['unidades']        = $albalin->unidades;
        $albalin_new['importe_unidad']  = $albalin->importe_unidad;
        $albalin_new['precio_coste']    = $albalin->precio_coste;
        $albalin_new['importe_venta']   = $albalin->importe_venta;
        $albalin_new['iva_id']          = $albalin->iva_id;
        $albalin_new['iva']             = $albalin->iva;
        $albalin_new['username']        = session('username');
        $albalin_new['created_at']      = Carbon::now();
        $albalin_new['updated_at']      = Carbon::now();

        DB::table('albalins')->insert($albalin_new);

    }

    private function crearCobro($albaran_new, $albaran_id_ant, $total_albaran){

        $cobros = Cobro::where('albaran_id',$albaran_id_ant)
                    ->orderBy('fecha','asc')
                    ->get();

        $sw=false;
        foreach ($cobros as $cobro){
            $sw=true;
            if ($cobro->fpago_id == 1)
                DB::table('cajas')
                    ->where('cobro_id', $cobro->id)
                    ->update(['cobro_id'  => null,
                              'manual'    => 'S',
                              'username'  => session('username'),
                              'updated_at'=>Carbon::now()
                            ]);
        }

        if ($sw===false)
            return;

        $cobro_new['fecha']      = $cobro->fecha;
        $cobro_new['albaran_id'] = $albaran_new->id;
        $cobro_new['empresa_id'] = $albaran_new->empresa_id;
        $cobro_new['cliente_id'] = $albaran_new->cliente_id;
        $cobro_new['fpago_id']   = 2; // siempre por transferencia
        $cobro_new['importe']    = $total_albaran;
        $cobro_new['notas']      = null;
        $cobro_new['username']   = $albaran_new->username;
        $cobro_new['created_at'] = Carbon::now();
        $cobro_new['updated_at'] = Carbon::now();


        DB::table('cobros')->insert($cobro_new);

    }
}
