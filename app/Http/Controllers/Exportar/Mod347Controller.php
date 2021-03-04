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

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
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

		// if ($dni != null)
		// 	$conddni = " AND dni = '".$dni."' ";
		// else
			$conddni = null;

		$union_resultado = "SELECT dni,razon, SUM(imptot)  AS rimptot, SUM(imptri1) AS rimptri1, SUM(imptri2) AS rimptri2, SUM(imptri3) AS rimptri3, SUM(imptri4) AS rimptri4 FROM (";

		// recuperaciones.
		$union1 = "SELECT dni,razon, SUM(l.importe_venta ) AS imptot,".
						      "SUM(IF( QUARTER( a.fecha_factura ) =1, l.importe_venta, 0 ) ) AS imptri1,".
			 			      "SUM(IF( QUARTER( a.fecha_factura ) =2, l.importe_venta, 0 ) ) AS imptri2, ".
			 			      "SUM(IF( QUARTER( a.fecha_factura ) =3, l.importe_venta, 0 ) ) AS imptri3,".
			 			      "SUM(IF( QUARTER( a.fecha_factura ) =4, l.importe_venta, 0 ) ) AS imptri4 ".
							  "FROM ".DB::getTablePrefix()."compras a, ".DB::getTablePrefix()."comlines l, ".DB::getTablePrefix()."clientes c ".
							  "WHERE a.empresa_id = ".session('empresa')->id." AND YEAR( a.fecha_factura ) = ".$ejercicio." ".
							  "AND a.id = l.compra_id ".
							  "AND a.cliente_id = c.id AND c.listar_347 = '1' ".$conddni." GROUP BY dni,razon UNION ";

		// ventas REBU.
		$union2 = "SELECT dni,razon, SUM(l.importe_venta ) AS imptot,".
				"SUM(IF( QUARTER( a.fecha_factura ) =1, l.importe_venta, 0 ) ) AS imptri1,".
				"SUM(IF( QUARTER( fecha_factura ) =2, l.importe_venta, 0 ) ) AS imptri2, ".
				"SUM(IF( QUARTER( fecha_factura ) =3, l.importe_venta, 0 ) ) AS imptri3,".
				"SUM(IF( QUARTER( fecha_factura ) =4, l.importe_venta, 0 ) ) AS imptri4 ".
				"FROM ".DB::getTablePrefix()."albaranes a, ".DB::getTablePrefix()."albalins l, ".DB::getTablePrefix()."clientes c ".
				"WHERE a.empresa_id = ".session('empresa')->id." AND YEAR( a.fecha_factura ) = ".$ejercicio." ".
				"AND a.tipo_id = 3 AND a.id = l.albaran_id ".
				"AND a.cliente_id = c.id  AND c.listar_347 = '1' ".$conddni." GROUP BY dni,razon UNION ";

		// ventas R GENERAL.
		$union3 = "SELECT dni,razon, SUM(ROUND(l.importe_venta+(l.importe_venta * l.iva / 100),2)) AS imptot,".
				"SUM(IF( QUARTER( fecha_factura ) =1, ROUND(l.importe_venta+(l.importe_venta * l.iva / 100),2), 0 ) ) AS imptri1,".
				"SUM(IF( QUARTER( fecha_factura ) =2, ROUND(l.importe_venta+(l.importe_venta * l.iva / 100),2), 0 ) ) AS imptri2, ".
				"SUM(IF( QUARTER( fecha_factura ) =3, ROUND(l.importe_venta+(l.importe_venta * l.iva / 100),2), 0 ) ) AS imptri3,".
				"SUM(IF( QUARTER( fecha_factura ) =4, ROUND(l.importe_venta+(l.importe_venta * l.iva / 100),2), 0 ) ) AS imptri4 ".
				"FROM ".DB::getTablePrefix()."albaranes a, ".DB::getTablePrefix()."albalins l, ".DB::getTablePrefix()."clientes c ".
				"WHERE a.empresa_id = ".session('empresa')->id." AND YEAR( a.fecha_factura ) = ".$ejercicio." ".
				"AND a.tipo_id = 4 AND a.id = l.albaran_id ".
				"AND a.cliente_id = c.id  AND c.listar_347 = '1' ".$conddni." GROUP BY dni,razon ";

		// // ventas R. General resto: implícito y exento.
		// $union4 = "SELECT dni,razon, SUM(l.importe ) AS imptot,".
		// 		"SUM(IF( QUARTER( a.fechafac ) =1, l.importe, 0 ) ) AS imptri1,".
		// 		"SUM(IF( QUARTER( fechafac ) =2, l.importe, 0 ) ) AS imptri2, ".
		// 		"SUM(IF( QUARTER( fechafac) =3, l.importe, 0 ) ) AS imptri3,".
		// 		"SUM(IF( QUARTER( fechafac ) =4, l.importe, 0 ) ) AS imptri4 ".
		// 		"FROM albaranes a, albalin l, clientes c ".
		// 		"WHERE a.empresa = ".session('empresa')->id." AND YEAR( a.fechafac ) = ".$ejercicio." ".
		// 		"AND a.tipo = 4 AND a.id = l.albaran AND l.linreg = 'N' AND ivarige <> 'G' ".
        // 		"AND a.cliente = c.id  AND c.imptcs = 'S' ".$conddni." GROUP BY dni,razon) ";

        return DB::select(DB::raw($union_resultado.$union1.$union2.$union3.") t GROUP BY t.dni, t.razon HAVING rimptot >".$impcorte." ORDER BY rimptot DESC"));

		// if ($limit == 0)
		// 	$select = $union_resultado.$union1.$union2.$union3.$union4." t GROUP BY t.dni, t.razon HAVING rimptot >".$impcorte." ORDER BY rimptot DESC";
		// else
		// 	$select = $union_resultado.$union1.$union2.$union3.$union4." t GROUP BY t.dni, t.razon HAVING rimptot >".$impcorte." ORDER BY rimptot DESC LIMIT ".$offset.",".$limit;

		// return $select;
    }

    private function ventasss($ejercicio,$impcorte=3000,$conddni=null){
        //public function eexcel(){
        //    $ejercicio = 2019;
          //  $impcorte=3000;

            // RECUPERACIONES
            $select = "dni,razon, SUM(".DB::getTablePrefix()."comlines.importe_venta ) AS rimptot,".
                    "SUM(IF( QUARTER( fecha_factura ) =1, ".DB::getTablePrefix()."comlines.importe_venta, 0 ) ) AS rimptri1,".
                    "SUM(IF( QUARTER( fecha_factura ) =2, ".DB::getTablePrefix()."comlines.importe_venta, 0 ) ) AS rimptri2, ".
                    "SUM(IF( QUARTER( fecha_factura ) =3, ".DB::getTablePrefix()."comlines.importe_venta, 0 ) ) AS rimptri3,".
                    "SUM(IF( QUARTER( fecha_factura ) =4, ".DB::getTablePrefix()."comlines.importe_venta, 0 ) ) AS rimptri4 ";

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

    private function ventas_zzzz($ejercicio,$impcorte=3000,$conddni=null){
    //public function eexcel(){
    //    $ejercicio = 2019;
      //  $impcorte=3000;

        $union_resultado = "SELECT dni,razon, SUM(imptot)  AS rimptot, SUM(imptri1) AS rimptri1, SUM(imptri2) AS rimptri2, SUM(imptri3) AS rimptri3, SUM(imptri4) AS rimptri4 FROM (";
        // RECUPERACIONES
        $select = "dni,razon, SUM(".DB::getTablePrefix()."comlines.importe_venta ) AS rimptot,".
				"SUM(IF( QUARTER( fecha_factura ) =1, ".DB::getTablePrefix()."comlines.importe_venta, 0 ) ) AS rimptri1,".
				"SUM(IF( QUARTER( fecha_factura ) =2, ".DB::getTablePrefix()."comlines.importe_venta, 0 ) ) AS rimptri2, ".
				"SUM(IF( QUARTER( fecha_factura ) =3, ".DB::getTablePrefix()."comlines.importe_venta, 0 ) ) AS rimptri3,".
                "SUM(IF( QUARTER( fecha_factura ) =4, ".DB::getTablePrefix()."comlines.importe_venta, 0 ) ) AS rimptri4 ";

        $union1 = DB::table('compras')
                    ->select(DB::raw($select))
                    ->join('comlines','compras.id','=','comlines.compra_id')
                    ->join('clientes','compras.cliente_id','=','clientes.id')
                    ->where('compras.empresa_id',session('empresa')->id)
                    ->whereYear('fecha_factura', $ejercicio)
                    ->where('listar_347', true)
                    ->groupBy('dni', 'razon');
                 //   ->havingRaw('SUM('.DB::getTablePrefix().'comlines.importe) > '.$impcorte );
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
                ->groupBy('dni', 'razon');
               // ->havingRaw('SUM('.DB::getTablePrefix().'albalins.importe_venta ) >'.$impcorte);
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
                ->groupBy('dni', 'razon');

        return DB::table('')->select(DB::raw('dni,razon, SUM(imptot)  AS rimptot, SUM(imptri1) AS rimptri1, SUM(imptri2) AS rimptri2, SUM(imptri3) AS rimptri3, SUM(imptri4) AS rimptri4'))
                ->union($union1)
                ->union($union2)
                ->union($union3)
                ->groupBy('dni', 'razon')
                ->having('rimptot','>',$impcorte)
                ->get();

                // ->orderBy('rimptot','desc')
            //    ->havingRaw('rimptot1 > '.$impcorte)
             //   ->havingRaw('SUM(ROUND(importe_venta+(importe_venta * iva / 100),2)) >'.$impcorte)
                //->having('rimptot','>',$impcorte)
              //  ->get();

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
