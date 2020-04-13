<?php

namespace App\Http\Controllers\Ventas;

use App\Tipo;
use App\Albalin;
use App\Albaran;
use App\Contador;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\FacturasExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\Albaranes\RangoFechaFacturacionRule;

class FacturacionVentasController extends Controller
{
    public function index()
    {
        if (session('empresa')->getFlag(6)){
            return abort(403,"Esta empresa no admite facturas!");
        }
        // if (!session('empresa')->getFlag(4)){
        //     return abort(403,"No se permiten nuevas ventas. Contactar administrador");
        // }

        if (request()->wantsJson())
            return [
                'tipos'=> Tipo::selTiposWithContador(),
                'ejercicio' => date('Y')
            ];
    }

     /**
     *
     * Facturación de compras: recuperaciones. Recalcula importes venta en líneas.
     *
     */
    public function albaranes(Request $request){

        if (!session('empresa')->getFlag(2)){
            return abort(403,"No se permiten ventas. Contactar administrador");
        }
        if (!session('empresa')->getFlag(4)){
            return abort(403,"No se permiten nuevas ventas. Contactar administrador");
        }

        $data=$request->validate([
            'tipo_id'     => ['required','integer'],
            'fecha_d'     => ['required','date', new RangoFechaFacturacionRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'     => ['required','date'],
            'accion'      => ['required','string'],
            'cobro'       => ['required','string'],
        ]);

       // $rango = trimestre($data['ejercicio'],$data['trimestre']);

        //      FACTURACIÓN
        if ($data['accion'] !='D'){
            $facturas = $this->facturarAlbaranes($data['fecha_d'],$data['fecha_h'],$data['tipo_id'],$data['cobro'],$data['accion']);
        }
        else{       // DESFACTURAR.
            $facturas = $this->desfacturarAlbaranes($data['fecha_d'],$data['fecha_h'],$data['tipo_id']);
        }

        if (request()->wantsJson())
            if ($facturas['estado'] == 'ok')
                return response($facturas['msg'], 200);
            else
                return abort(404,$facturas['msg']);

    }


    private function facturarAlbaranes($d,$h, $tipo_id, $cobro, $tipo_fecha){

        $albaranes = $this->pendientesDeFacturar($d,$h, $tipo_id, $cobro);

        $ejercicio = getEjercicio($d);

        $max_fecha_factura = Albaran::whereYear('fecha_factura',$ejercicio)
                                        ->where('tipo_id', $tipo_id)
                                        ->where('tipo_factura', 2)
                                        ->max('fecha_factura');


        foreach ($albaranes as $row){

          //  if (session('empresa')->id != session('empresa')->deposito_empresa_id)
            if ($tipo_id == 3)
                if ($this->verificarSiHayProductosEnDeposito($row->id)){
                    return abort(411, 'Se han encontrado albaranes sin reubicar, reubicar antes de continuar!!');
                }
        }

        if ($albaranes->count() > 0){
            $contador =  Contador::where('ejercicio',$ejercicio)
                                  ->where('tipo_id', $tipo_id)
                        ->lockForUpdate()->firstOrFail();
        }else{
            return ['estado'=>'ko', 'reg'=>0, 'msg'=> 'No se han encontrado facturas'];
        }

        //$albaranes = $albaranes->get();

        $i=0;
        foreach ($albaranes as $row){
            $i++;

          //  if (session('empresa')->id != session('empresa')->deposito_empresa_id)
            if ($tipo_id == 3)
                if ($this->verificarSiHayProductosEnDeposito($row->id)){
                    return abort(411, 'Se han encontrado albaranes sin reubicar, reubicar antes de continuar!!');

                }
            $fecha_factura = ($tipo_fecha == 'F') ? $h : $row->fecha; // fecha fija o fecha cobro.

            if ($fecha_factura < $max_fecha_factura)
                return abort(411, 'Se han encontrado una factura anterior y rompe secuencia facturación. Proceso abortado! Alb: '.$row->albaran);

            $contador->ult_factura_auto++;


            $data = [
                'serie_factura' => $contador->serie_factura_auto,
                'factura'       => $contador->ult_factura_auto,
                'fecha_factura' => $fecha_factura,
                'factura_txt'   => $ejercicio.$contador->serie_factura_auto.$contador->ult_factura_auto,
                'tipo_factura'  => 2,
                'username'      => session('username')
            ];

            Albaran::where('id', $row->id)->update($data);
        }

        $contador->update(['ult_factura_auto'=>$contador->ult_factura_auto]);

        return ['estado'=>'ok', 'reg'=>$i, 'msg'=> 'Procesadas '.$i.' facturas'];
    }

    private function verificarSiHayProductosEnDeposito($albaran_id){

        $lineas = Albalin::with('producto')->where('albaran_id', $albaran_id)->get();


        foreach ($lineas as $row){

            //\Log::info($albaran_id);

            //if ($row->producto->destino_empresa_id != $row->producto->empresa_id || $row->producto->cliente_id > 0){
            if ($row->producto->empresa_id != $row->empresa_id){
                return true;
                break;
            }

        }

        return false;

    }

    private function pendientesDeFacturar($d, $h, $tipo_id, $cobro){

            if ($cobro == 'T')
                $fpago = array(1,2,3,4);
            elseif($cobro == "B")
                $fpago = array(2,3,4);
            else
                $fpago = array(1);

            return DB::table('albaranes')
                    ->select(DB::raw(DB::getTablePrefix().'albaranes.id,'.
                                     DB::getTablePrefix().'albaranes.albaran,'.
                                     DB::getTablePrefix().'albaranes.iva_no_residente, MAX('.
                                     DB::getTablePrefix().'cobros.fecha) AS fecha'))
                //    ->join('clientes', 'cliente_id', '=', 'clientes.id')
                    ->join('cobros', 'albaran_id', '=', 'albaranes.id')
                    ->where('albaranes.empresa_id', session('empresa')->id)
                    ->where('tipo_id', $tipo_id)
                    ->where('fase_id', 11)
                    // ->where('factura', 0)
                    ->whereNull('factura')
                    ->where('facturar', true)
                    ->whereNull('albaranes.deleted_at')
                    ->whereIn('cobros.fpago_id', $fpago)
                    ->groupBy('albaranes.id','albaran','iva_no_residente')
                    ->havingRaw('MAX('.DB::getTablePrefix().'cobros.fecha) >= ? AND MAX('.DB::getTablePrefix().'cobros.fecha) <= ?',[$d,$h])
                    ->orderBy('fecha')
                    ->get();


    }

    private function desfacturarAlbaranes($d, $h, $tipo_id){

        $ejercicio = getEjercicio($d);

        $contador =  Contador::where('ejercicio',$ejercicio)
                        ->where('tipo_id', $tipo_id)
                        ->lockForUpdate()->firstOrFail();

        $min = Albaran::fecha($d, $h,'F')
                    ->where('tipo_id',$tipo_id)
                    ->where('tipo_factura', 2)
                    ->whereDate('fecha_factura','>=',$d)
                    ->whereDate('fecha_factura','<=',$h)
                    ->min('factura');


        if ($min == 0)
            return ['estado'=>'ko', 'reg'=>0,'msg'=>'No hay nada para desfacturar!'];
        else{
            $max = Albaran::fecha($d, $h,'F')
                        ->where('tipo_id',$tipo_id)
                        ->where('tipo_factura', 2)
                        ->whereDate('fecha_factura','>=',$d)
                        ->whereDate('fecha_factura','<=',$h)
                        ->max('factura');
        }


        $data = [
            'serie_factura' => null,
            'factura'       => null,
            'fecha_factura' => null,
            'factura_txt'   => null,
            'tipo_factura'  => 0,
            'fecha_notificacion'=> null,
            'username'      => session('username')
        ];



        $reg =  Albaran::fecha($d, $h, 'F')
                    ->where('tipo_id',$tipo_id)
                    ->where('tipo_factura', 2)
                    ->whereDate('fecha_factura','>=',$d)
                    ->whereDate('fecha_factura','<=',$h)
                    ->update($data);

                 //   \Log::info('min:'.$min.' max:'.$max.' reg:'.$reg);

        if (($max - $min +1) == $reg){
            $contador->update(['ult_factura_auto' => ($min-1)]);
            return ['estado'=>'ok', 'reg'=>$reg,'msg'=> 'Desfacturadas '.$reg.' facturas'];
        }else{
            return ['estado'=>'ko', 'reg'=>$reg, 'msg'=> 'Desfacturación OK. Revisar Contador!!'];
        }



    }

}
