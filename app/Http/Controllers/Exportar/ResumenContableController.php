<?php

namespace App\Http\Controllers\Exportar;

use stdClass;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use App\Exports\SituacionExport;
use App\Exports\ResContableExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ResumenContableController extends Controller
{

    public function resconta(Request $request){

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $request->validate([
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
        ]);

        return $this->detalle($data);

    }

    private function detalle($data)
    {

        $select='"COMPRAS-recu" AS nom_com,'.DB::getTablePrefix().'clases.nombre AS clase, '.DB::getTablePrefix().'comlines.quilates AS quilates, SUM(peso_gr) AS peso_gr, SUM('.DB::getTablePrefix().'comlines.importe) AS importe,  SUM(0) AS importe_venta';

        $union0 = DB::table('compras')
            ->select(DB::raw($select))
            ->join('comlines','compras.id','=','compra_id')
            ->join('clases','comlines.clase_id','=','clases.id')
            ->where('compras.empresa_id', session('empresa')->id)
            ->where('compras.tipo_id', 1)
            ->whereDate('fecha_compra','>=', $data['fecha_d'])
            ->whereDate('fecha_compra','<=', $data['fecha_h'])
            ->groupBy(DB::raw('nom_com,clase,quilates WITH ROLLUP'))
            ->get();


        $select='"COMPRAS" AS nom_com,'.DB::getTablePrefix().'clases.nombre AS clase, '.DB::getTablePrefix().'comlines.quilates AS quilates, SUM(peso_gr) AS peso_gr, SUM('.DB::getTablePrefix().'comlines.importe) AS importe,  SUM(0) AS importe_venta';

        $union1 = DB::table('compras')
            ->select(DB::raw($select))
            ->join('comlines','compras.id','=','compra_id')
            ->join('clases','comlines.clase_id','=','clases.id')
            ->where('compras.empresa_id', session('empresa')->id)
            ->where('compras.tipo_id', 2)
            ->whereDate('fecha_compra','>=', $data['fecha_d'])
            ->whereDate('fecha_compra','<=', $data['fecha_h'])
            ->groupBy(DB::raw('nom_com,clase,quilates WITH ROLLUP'))
            ->get();

        $select='"FACTURADO REBU" AS nom_com,'.DB::getTablePrefix().'clases.nombre AS clase, '.DB::getTablePrefix().'productos.quilates AS quilates, SUM(peso_gr) AS peso_gr, SUM('.DB::getTablePrefix().'albalins.precio_coste) AS importe,  SUM(importe_venta) AS importe_venta';
        $union2 = DB::table('albaranes')
            ->select(DB::raw($select))
            ->join('albalins','albaranes.id','=','albaran_id')
            ->join('productos','productos.id','=','producto_id')
            ->join('clases','productos.clase_id','=','clases.id')
            ->where('albaranes.empresa_id', session('empresa')->id)
            ->where('albaranes.tipo_id', 3)
            ->whereDate('fecha_factura','>=', $data['fecha_d'])
            ->whereDate('fecha_factura','<=', $data['fecha_h'])
            ->whereNull('albaranes.deleted_at')
            ->groupBy(DB::raw('nom_com,clase,quilates WITH ROLLUP'))
            ->get();

        //return $union1->merge($union2);
        // $collection = $union1->merge($union2);
        // $filtered = $collection->whereIn('nom_com',['COMPRAS','VENTAS']);
        // return $filtered->all();



        $select='"FACTURADO VENTAS" AS nom_com,'.DB::getTablePrefix().'clases.nombre AS clase, '.DB::getTablePrefix().'productos.quilates AS quilates, SUM(peso_gr) AS peso_gr, SUM('.DB::getTablePrefix().'albalins.precio_coste) AS importe,  SUM(importe_venta) AS importe_venta';
        $union3 = DB::table('albaranes')
            ->select(DB::raw($select))
            ->join('albalins','albaranes.id','=','albaran_id')
            ->join('productos','productos.id','=','producto_id')
            ->join('clases','productos.clase_id','=','clases.id')
            ->where('albaranes.empresa_id', session('empresa')->id)
            ->where('tipo_id', 4)
            ->whereDate('fecha_factura','>=', $data['fecha_d'])
            ->whereDate('fecha_factura','<=', $data['fecha_h'])
            ->whereNull('albaranes.deleted_at')
            ->groupBy(DB::raw('nom_com,clase,quilates WITH ROLLUP'))
            // ->union($union2)
            // ->union($union1)
            ->get();

        //$collection = $union1->merge($union2)->merge($union3);
        //->whereIn('nom_com',['COMPRAS','VENTAS','VENTAS REBU']);

      //  $collection = $collection->whereIn('nom_com',['COMPRAS','VENTAS','VENTAS REBU']);

        return $union0->merge($union1)
                      ->merge($union2)
                      ->merge($union3);
                    //   ->whereIn('nom_com',['COMPRAS','VENTAS'])
                    //   ->all();


    }

     /**
     * Recibe las facturas por request, previamente de $this->lisfac()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new ResContableExport($request->data, 'Resumen '.session('empresa')->razon), 'resconta.xlsx');


    }


    // $select='"COMPRAS" AS nom_com,'.DB::getTablePrefix().'clases.nombre AS clase, '.DB::getTablePrefix().'comlines.quilates AS quilates, SUM(peso_gr) AS peso_gr, SUM('.DB::getTablePrefix().'comlines.importe) AS importe,  SUM(0) AS importe_venta';

    // $union1 = DB::table('compras')
    //     ->select(DB::raw($select))
    //     ->join('comlines','compras.id','=','compra_id')
    //     ->join('clases','comlines.clase_id','=','clases.id')
    //     ->where('compras.empresa_id', session('empresa')->id)
    //     ->whereDate('fecha_compra','>=', $data['fecha_d'])
    //     ->whereDate('fecha_compra','<=', $data['fecha_h'])
    //     ->groupBy('nom_com','clase','quilates');

    // $select='"VENTAS REBU" AS nom_com,'.DB::getTablePrefix().'clases.nombre AS clase, '.DB::getTablePrefix().'productos.quilates AS quilates, SUM(peso_gr) AS peso_gr, SUM('.DB::getTablePrefix().'albalins.precio_coste) AS importe,  SUM(importe_venta) AS importe_venta';
    // $union2 = DB::table('albaranes')
    //     ->select(DB::raw($select))
    //     ->join('albalins','albaranes.id','=','albaran_id')
    //     ->join('productos','productos.id','=','producto_id')
    //     ->join('clases','productos.clase_id','=','clases.id')
    //     ->where('albaranes.empresa_id', session('empresa')->id)
    //     ->where('albaranes.tipo_id', 3)
    //     ->whereDate('fecha_factura','>=', $data['fecha_d'])
    //     ->whereDate('fecha_factura','<=', $data['fecha_h'])
    //     ->where('albaranes.deleted_at', null)
    //     ->groupBy('nom_com','clase','quilates');

    // $select='"VENTAS" AS nom_com,'.DB::getTablePrefix().'clases.nombre AS clase, '.DB::getTablePrefix().'productos.quilates AS quilates, SUM(peso_gr) AS peso_gr, SUM('.DB::getTablePrefix().'albalins.precio_coste) AS importe,  SUM(importe_venta) AS importe_venta';
    // $union3 = DB::table('albaranes')
    //     ->select(DB::raw($select))
    //     ->join('albalins','albaranes.id','=','albaran_id')
    //     ->join('productos','productos.id','=','producto_id')
    //     ->join('clases','productos.clase_id','=','clases.id')
    //     ->where('albaranes.empresa_id', session('empresa')->id)
    //     ->where('tipo_id', 4)
    //     ->whereDate('fecha_factura','>=', $data['fecha_d'])
    //     ->whereDate('fecha_factura','<=', $data['fecha_h'])
    //     ->where('albaranes.deleted_at', null)
    //     ->groupBy('nom_com','clase','quilates')
    //     ->union($union2)
    //     ->union($union1)
    //     ->orderBy('nom_com')
    //     ->orderBy('clase')
    //     ->orderBy('quilates')
    //     ->get();

    // //return $union3;

    // $union3->sum(function ($row) {
    //     return count($row['COMPRAS']);
    // });

    // $o = new stdClass();
}
