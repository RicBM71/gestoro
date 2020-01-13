<?php

namespace App\Http\Controllers\Ventas;

use App\Tipo;
use App\Albalin;
use App\Albaran;
use App\Contador;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use App\Exports\FacturasExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class FacturacionVentasController extends Controller
{
    public function index()
    {
        if (!session('empresa')->getFlag(2)){
            return abort(403,"No se permiten ventas. Contactar administrador");
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
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
            'accion'      => ['required','string'],
            'cobro'       => ['required','string'],
        ]);

       // $rango = trimestre($data['ejercicio'],$data['trimestre']);

        //      FACTURACIÓN
        if ($data['accion']=='F'){


            $facturas = $this->facturarAlbaranes($data['fecha_d'],$data['fecha_h'],$data['tipo_id'],$data['cobro']);
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


    private function facturarAlbaranes($d,$h, $tipo_id, $cobro){

        $i=0;

        $albaranes = Albaran::pendientesDeFacturar($d,$h, $tipo_id, $cobro);

        $ejercicio = getEjercicio($d);

        if ($albaranes->count() > 0){
            $contador =  Contador::where('ejercicio',$ejercicio)
                                  ->where('tipo_id', $tipo_id)
                        ->lockForUpdate()->firstOrFail();
        }else{
            return ['estado'=>'ko', 'reg'=>0, 'msg'=> 'No se han encontrado facturas'];
        }

        //$albaranes = $albaranes->get();

        foreach ($albaranes as $row){
            $i++;

            $contador->ult_factura_auto++;

            $data = [
                'serie_factura' => $contador->serie_factura_auto,
                'factura'       => $contador->ult_factura_auto,
                'fecha_factura' => $row->fecha,
                'factura_txt'   => $ejercicio.$contador->serie_factura_auto.$contador->ult_factura_auto,
                'tipo_factura'  => 2,
                'username'      => session('username')
            ];

            Albaran::where('id', $row->id)->update($data);
        }

        $contador->update(['ult_factura_auto'=>$contador->ult_factura_auto]);

        return ['estado'=>'ok', 'reg'=>$i, 'msg'=> 'Procesadas '.$i.' facturas'];
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
            'factura'       => 0,
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

        if ($max - $min == $reg){
            $contador->update(['ult_factura_auto' => $min]);
            return ['estado'=>'ok', 'reg'=>$reg,'msg'=> 'Desfacturadas '.$reg.' facturas'];
        }else{
            return ['estado'=>'ko', 'reg'=>$reg, 'msg'=> 'Desfacturación OK. Revisar Contador!!'];
        }



    }

      /**
     *
     * Relación de facturas de recuperaciones.
     *
     */
    public function lisfac(Request $request){

        $data=$request->validate([
            'tipo_id'      => ['required','integer'],
            'tipo_factura' => ['required','integer'],
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],

        ]);

        $facturas = Albaran::with(['cliente','albalins'])
                        ->tipo($data['tipo_id'])
                        ->fecha($data['fecha_d'], $data['fecha_h'], "F")
                        ->where('factura','>', 0)
                        ->where('tipo_factura', $data['tipo_factura'])
                        ->orderBy('factura')
                        ->get();

        $collection=[];
        foreach ($facturas as $row){

            $lineas = DB::table('albalins')
                        ->select(DB::raw('iva_id, iva AS iva, rebu, SUM(importe_venta) AS importe_venta, SUM(precio_coste) AS precio_coste'))
                        ->join('ivas', 'ivas.id', '=', 'iva_id')
                        ->where('albaran_id', $row->id)
                        ->whereNull('deleted_at')
                        ->groupBy('iva_id','iva', 'rebu')
                        ->get();

            foreach ($lineas as $albalin){

                if ($albalin->rebu){
                    $beneficio = $albalin->importe_venta - $albalin->precio_coste;
                    $base_iva = round($beneficio / (1+($albalin->iva/100)),2);
                    $iva = $beneficio - $base_iva;
                    $rebu = "REBU";
                }else{
                    $rebu="";
                    $beneficio = 0;
                    $base_iva = $albalin->importe_venta;
                    $iva = round($albalin->importe_venta * $albalin->iva / 100, 2);
                }


            // $pvp = $coste = $bene = 0;

            // foreach($row->albalins as $li){
            //     $pvp+= $li->importe_venta;
            //     $coste+= $li->precio_coste;
            //     $bene = $pvp - $coste;
            // }

            // // como no hay más de dos tipos de iva, puedo 'atajar' así:

            // $base = round($bene / (1+($li->iva/100)),2);
            // $iva = round($bene - $base,2);

                $collection[]=[
                    'facser'         => $row->serie_factura.'-'.$row->factura,
                    'factura'        => $row->factura,
                    'fecha_factura'  => Carbon::parse($row->fecha_factura)->format('Y-m-d'),
                    'fecha_compra'   => Carbon::parse($row->fecha_albaran)->format('Y-m-d'),
                    'alb_ser'        => $row->alb_ser,
                    'dni'            => $row->cliente->dni,
                    'razon'          => $row->cliente->razon,
                    'pvp'            => round($albalin->importe_venta, 2),
                    'coste'          => round($albalin->precio_coste),
                    'bene'           => $beneficio,
                    'base'           => $base_iva,
                    'iva'            => $iva,
                    'tipo_id'        => $row->tipo_id,
                    'por_iva'        => $albalin->iva,
                    'rebu'           => $rebu,
                    'id'             => $row->id
                ];
            }

        }

        return collect($collection);

    }

      /**
     * Recibe las facturas por request, previamente de $this->lisfac()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new FacturasExport($request->data), 'fac.xlsx');

    }

}
