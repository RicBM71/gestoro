<?php

namespace App\Http\Controllers\Utilidades;

use App\Libro;
use App\Compra;
use App\Contador;
use App\Scopes\EmpresaScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ContadorCheckController extends Controller
{

    public function index($ejercicio=0){


        if ($ejercicio == 0){
            $ejercicio = date('Y');
        }


        $data = $this->checkLibro($ejercicio);



        //return $data;

        if (request()->wantsJson())
            return $data;

    }

    private function checkLibro($ejercicio){

        $libros = Libro::withoutGlobalScope(EmpresaScope::class)
                    ->select('empresas.nombre AS empresa','grupo_id', 'empresa_id','ult_compra','ult_factura','serie_com')
                    ->join('empresas','empresa_id','=','empresas.id')
                    ->whereIn('libros.empresa_id', session('empresas_usuario'))
                    ->where('ejercicio', $ejercicio)->orderBy('empresa_id')->get();


        //dd($libros);
        $contadores = Contador::withoutGlobalScope(EmpresaScope::class)
                        ->select('empresas.nombre AS empresa','empresa_id','ult_factura','ult_factura_auto','ult_factura_abono','tipos.nombre','tipo_id')
                        ->join('empresas','empresa_id','=','empresas.id')
                        ->join('tipos','tipo_id','=','tipos.id')
                        ->whereIn('contadores.empresa_id', session('empresas_usuario'))
                        ->where('ejercicio', $ejercicio)->orderBy('empresa_id')->get();

        $data=array();
        foreach ($libros as $row) {

            $compras = DB::table('compras')->select(DB::raw('count(*) AS reg'))
                                ->where('empresa_id', $row->empresa_id)
                                ->whereYear('fecha_compra', $ejercicio)
                                ->where('grupo_id', $row->grupo_id)
                                ->where('serie_com', $row->serie_com)
                                ->first();

            $recuperaciones = DB::table('compras')->select(DB::raw('count(*) AS reg'))
                                ->where('empresa_id', $row->empresa_id)
                                ->whereYear('fecha_factura', $ejercicio)
                                ->where('grupo_id', $row->grupo_id)
                                ->first();

            if ($row->ult_compra > 0 && $compras->reg > 0)
               $data[]=[
                    'empresa'       => $row->empresa,
                    'operacion'     => 'COMPRAS',
                    'nombre'        => $row->serie_com,
                    'contador'      => $row->ult_compra,
                    'recuento'      => $compras->reg,
                ];

            if ($row->ult_factura > 0 && $recuperaciones->reg > 0)
                $data[]=[
                    'empresa'       => $row->empresa,
                    'operacion'     => 'FACTURAS DE RECUPERACIONES',
                    'nombre'        => $row->serie_com,
                    'contador'      => $row->ult_factura,
                    'recuento'      => $recuperaciones->reg,
                ];

        }

        foreach ($contadores as $row) {

            $albaranes = DB::table('albaranes')->select(DB::raw('tipo_factura, count(*) AS reg'))
                                ->where('empresa_id', $row->empresa_id)
                                ->whereYear('fecha_factura', $ejercicio)
                                ->where('tipo_id', $row->tipo_id)
                                ->groupBy('tipo_factura')
                                ->get();

           // \Log::info($row->ult_factura_auto);

            foreach ($albaranes as $albaran) {

                if ($albaran->tipo_factura == 1){
                    $factura = $row->ult_factura;
                    $tipofac = 'FACTURAS s/MANUAL';
                }
                elseif ($albaran->tipo_factura == 2){
                    $factura = $row->ult_factura_auto;
                    $tipofac = 'FACTURAS s/AUTO';
                }
                elseif ($albaran->tipo_factura == 3){
                    $factura = $row->ult_factura_abono;
                    $tipofac = 'FACTURAS s/ABONOS';
                }

                if ($factura == 0 && $albaran->reg == 0)
                    continue;

                $data[]=[
                    'empresa'    => $row->empresa,
                    'operacion'  => $row->nombre,
                    'nombre'     => $tipofac,
                    'contador'   => $factura,
                    'recuento'   => $albaran->reg,
                ];

            }

        }


        return $data;

    }

}
