<?php

namespace App\Http\Controllers\Exportar;

use Illuminate\Http\Request;
use App\Exports\Mod347Export;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class Mod347Controller extends Controller
{

    public function excel(Request $request){

        if (!auth()->user()->hasRole('Gestor')){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Gestor');
        }

        $data = $request->validate([
            'modelo' => 'required',
            'ejercicio'=> ['required','integer'],
            'imp_corte'=> ['required','numeric']
        ]);

        if ($data['modelo'] == 'C'){
            $registros = $this->compras($data['ejercicio'], $data['imp_corte']);
            $titulo = "Adquisiciones de bienes y servicios";
        }else{
            $registros = $this->ventas($data['ejercicio'], $data['imp_corte']);
            $titulo = "Entrega de bienes y prestaciones de servicios";
        }

        return Excel::download(new Mod347Export($registros, $titulo), 'mod347.xlsx');

    }

    private function compras($ejercicio,$impcorte=3000,$conddni=null){

        $select = "dni,razon, SUM(".DB::getTablePrefix()."comlines.  importe ) AS rimptot,".
				"SUM(IF( QUARTER( fecha_compra ) =1, ".DB::getTablePrefix()."comlines.importe, 0 ) ) AS rimptri1,".
				"SUM(IF( QUARTER( fecha_compra ) =2, ".DB::getTablePrefix()."comlines.importe, 0 ) ) AS rimptri2, ".
				"SUM(IF( QUARTER( fecha_compra ) =3, ".DB::getTablePrefix()."comlines.importe, 0 ) ) AS rimptri3,".
                "SUM(IF( QUARTER( fecha_compra ) =4, ".DB::getTablePrefix()."comlines.importe, 0 ) ) AS rimptri4 ";

        $data = DB::table('compras')
                    ->select(DB::raw($select))
                    ->join('comlines','compras.id','=','comlines.compra_id')
                    ->join('clientes','compras.cliente_id','=','clientes.id')
                    ->where('compras.empresa_id',session('empresa')->id)
                    ->whereYear('fecha_compra', $ejercicio)
                    ->where('listar_347', true)
                    ->groupBy('dni', 'razon')
                    ->having('rimptot','>',$impcorte)->get();
                    //->orderBy('rimptot','desc')->get();


        return $data;
    }

    private function ventas($ejercicio,$impcorte=3000,$conddni=null){
    //public function eexcel(){
    //    $ejercicio = 2019;
      //  $impcorte=3000;

        // RECUPERACIONES
        $select = "dni,razon, SUM(".DB::getTablePrefix()."comlines.importe ) AS rimptot,".
				"SUM(IF( QUARTER( fecha_factura ) =1, ".DB::getTablePrefix()."comlines.importe, 0 ) ) AS rimptri1,".
				"SUM(IF( QUARTER( fecha_factura ) =2, ".DB::getTablePrefix()."comlines.importe, 0 ) ) AS rimptri2, ".
				"SUM(IF( QUARTER( fecha_factura ) =3, ".DB::getTablePrefix()."comlines.importe, 0 ) ) AS rimptri3,".
                "SUM(IF( QUARTER( fecha_factura ) =4, ".DB::getTablePrefix()."comlines.importe, 0 ) ) AS rimptri4 ";

        $union1 = DB::table('compras')
                    ->select(DB::raw($select))
                    ->join('comlines','compras.id','=','comlines.compra_id')
                    ->join('clientes','compras.cliente_id','=','clientes.id')
                    ->where('compras.empresa_id',session('empresa')->id)
                    ->whereYear('fecha_factura', $ejercicio)
                    ->where('listar_347', true)
                    ->groupBy('dni', 'razon')
                    ->havingRaw('SUM('.DB::getTablePrefix().'comlines.importe) > '.$impcorte );
                    //->having('rimptot','>',$impcorte);

        // VENTAS REBU

        $select = "dni,razon, SUM(".DB::getTablePrefix()."albalins.importe_venta ) AS rimptot,".
                "SUM(IF( QUARTER( fecha_factura ) =1, ".DB::getTablePrefix()."albalins.importe_venta, 0 ) ) AS rimptri1,".
                "SUM(IF( QUARTER( fecha_factura ) =2, ".DB::getTablePrefix()."albalins.importe_venta, 0 ) ) AS rimptri2, ".
                "SUM(IF( QUARTER( fecha_factura ) =3, ".DB::getTablePrefix()."albalins.importe_venta, 0 ) ) AS rimptri3,".
                "SUM(IF( QUARTER( fecha_factura ) =4, ".DB::getTablePrefix()."albalins.importe_venta, 0 ) ) AS rimptri4 ";

        $union2 = DB::table('albaranes')
                ->select(DB::raw($select))
                ->join('albalins','albaranes.id','=','albalins.albaran_id')
                ->join('clientes','albaranes.cliente_id','=','clientes.id')
                ->where('albaranes.empresa_id',session('empresa')->id)
                ->where('tipo_id', 3)
                ->whereYear('fecha_factura', $ejercicio)
                ->where('listar_347', true)
                ->whereNull('albaranes.deleted_at')
                ->groupBy('dni', 'razon')
                ->havingRaw('SUM('.DB::getTablePrefix().'albalins.importe_venta ) >'.$impcorte);
//                ->having('rimptot','>',$impcorte);


        // REGIMEN GENERAL VENTAS, CON IVA y exentas.
        $select = "dni,razon, SUM(ROUND(importe_venta+(importe_venta * iva / 100),2)) AS rimptot,".
                                "SUM(IF( QUARTER( fecha_factura ) =1, ROUND(importe_venta+(importe_venta * iva / 100),2), 0 ) ) AS rimptri1,".
                                "SUM(IF( QUARTER( fecha_factura ) =2, ROUND(importe_venta+(importe_venta * iva / 100),2), 0 ) ) AS rimptri2, ".
                                "SUM(IF( QUARTER( fecha_factura ) =3, ROUND(importe_venta+(importe_venta * iva / 100),2), 0 ) ) AS rimptri3,".
                                "SUM(IF( QUARTER( fecha_factura ) =4, ROUND(importe_venta+(importe_venta * iva / 100),2), 0 ) ) AS rimptri4 ";

        $union3 = DB::table('albaranes')
                ->select(DB::raw($select))
                ->join('albalins','albaranes.id','=','albalins.albaran_id')
                ->join('clientes','albaranes.cliente_id','=','clientes.id')
                ->where('albaranes.empresa_id',session('empresa')->id)
                ->where('tipo_id', 4)
                ->whereYear('fecha_factura', $ejercicio)
                ->where('listar_347', true)
                ->whereNull('albaranes.deleted_at')
                ->groupBy('dni', 'razon')
                ->union($union1)
                ->union($union2)
                ->orderBy('rimptot','desc')
                ->havingRaw('SUM(ROUND(importe_venta+(importe_venta * iva / 100),2)) >'.$impcorte)
                //->having('rimptot','>',$impcorte)
                ->get();

        //         ->select(DB::raw($select))
        //         ->join('albalins','albaranes.id','=','albalins.albaran_id')
        //         ->join('clientes','albaranes.cliente_id','=','clientes.id')
        //         ->where('albaranes.empresa_id',session('empresa')->id)
        //         ->where('tipo_id', 4)
        //         ->whereYear('fecha_factura', $ejercicio)
        //         ->where('listar_347', true)
        //         ->where('albaranes.deleted_at', null)
        //         ->groupBy('dni', 'razon')
        //         ->union($union1)
        //         ->union($union2)
        //    //     ->having('rimptot','>',$impcorte)
        //         ->orderBy('rimptot','desc')->toSql());


        return $union3;
    }

}
