<?php

namespace App\Http\Controllers\Ventas;

use App\Tipo;
use App\Grupo;
use App\Compra;
use App\Albaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use App\Exports\FacturasExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ListarFacturasController extends Controller
{

    public function index()
    {

        if (request()->wantsJson())
            return [
                'grupos'    => Grupo::selGruposRebu(),
                'tipos'     => Tipo::selTiposWithContador(),
                'ejercicio' => date('Y')
            ];
    }

     /**
     *
     * Relación de facturas de recuperaciones.
     *
     */
    public function lisrecu(Request $request){

        $data=$request->validate([
            'grupo_id'  => ['required','integer'],
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
        ]);

        //$rango = trimestre($data['ejercicio'],$data['trimestre']);

        $facturas = Compra::with(['cliente','comlines'])
                        ->grupo($data['grupo_id'])
                        ->fecha($data['fecha_d'], $data['fecha_h'], "F")
                        ->where('factura','>', 0)
                        ->orderBy('factura')
                        ->get();

        $collection=[];
        foreach ($facturas as $row){

            $pvp = $coste = $bene = 0;

            foreach($row->comlines as $li){
                $pvp+= $li->importe_venta;
                $coste+= $li->importe;
                $bene = $pvp - $coste;
            }

            // como no hay más de dos tipos de iva, puedo 'atajar' así:

            $base = round($bene / (1+($li->iva/100)),2);
            $iva = round($bene - $base,2);

            $collection[]=[
                'facser'         => $row->fac_ser,
                'factura'        => $row->factura,
                'fecha_factura'  => Carbon::parse($row->fecha_factura)->format('Y-m-d'),
                'fecha_compra'   => Carbon::parse($row->fecha_compra)->format('Y-m-d'),
                'alb_ser'        => $row->alb_ser,
                'dni'            => $row->cliente->dni,
                'razon'          => $row->cliente->razon,
                'pvp'            => round($pvp,2),
                'coste'          => round($coste, 2),
                'bene'           => round($bene),
                'base'           => $base,
                'iva'            => $iva,
                'tipo_id'        => $row->tipo_id,
                'por_iva'        => $li->iva,
                'rebu'           => 'REBU',
                'id'             => $row->id
            ];

        }
        return collect($collection);

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
                    'facser'         => $row->fac_ser,
                    'factura'        => $row->factura,
                    'fecha_factura'  => Carbon::parse($row->fecha_factura)->format('Y-m-d'),
                    'fecha_compra'   => Carbon::parse($row->fecha_albaran)->format('Y-m-d'),
                    'alb_ser'        => $row->alb_ser,
                    'dni'            => $row->cliente->dni,
                    'razon'          => $row->cliente->razon,
                    'pvp'            => round($albalin->importe_venta, 2),
                    'coste'          => round($albalin->precio_coste, 2),
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
     * Recibe las facturas por request, previamente de $this->lisrecu()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new FacturasExport($request->data), 'fac.xlsx');

    }

}
