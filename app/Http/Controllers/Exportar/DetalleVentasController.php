<?php

namespace App\Http\Controllers\Exportar;

use App\Tipo;
use App\Clase;
use App\Cobro;
use App\Albalin;
use App\Albaran;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use Illuminate\Support\Facades\DB;
use App\Exports\DetalleVentasExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class DetalleVentasController extends Controller
{
    public function index(){


        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        if (request()->wantsJson())
            return [
                'tipos' => Tipo::selTiposVen(),
                'clases' => Clase::selGrupoClase()
            ];

    }

    public function submit(Request $request){

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $request->validate([
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
            'tipo_id'   => ['required','integer'],
            'operacion'=> ['required','string'],
        ]);

        return $this->detalle($data);

    }

    private function detalle($data){


        if ($data['operacion'] == 'N')
            $albaranes = $this->noFacturados($data);
        elseif ($data['operacion'] == 'F')
            $albaranes = $this->facturados($data);
        else
            $albaranes = $this->todos($data);

        if ($albaranes == null)
            return [];

        $row_id = 0;
        $alb_ant = -1;
        $arr=array();
        foreach ($albaranes as $row){

            if ($alb_ant != $row->id){
                $cobros = Cobro::selectRaw('fpago_id, SUM(importe) AS importe')->where('albaran_id', $row->id)->groupBy('fpago_id')->get();

                $cobro_alb=array(0,0);
                foreach ($cobros as $cobro){
                    if ($cobro->fpago_id == 1)
                        $cobro_alb[0] += $cobro->importe;
                    else
                        $cobro_alb[1] += $cobro->importe;
                }
                $alb_ant = $row->id;
            }
            else{
                $cobro_alb=array(0,0);
            }

            //

            $albaran = Albaran::find($row->id);

            $albalins = Albalin::with(['producto' => function ($query) {
                                                    $query->withTrashed();},
                                        'producto.clase'])->where('albaran_id', $row->id)->get();

            $primera = true;
            foreach ($albalins as $albalin){

                $row_id++;

                $margen = $albalin->importe_venta - $albalin->precio_coste;
                $dif_rel = $albalin->precio_coste <> 0 ? $margen / $albalin->precio_coste * 100 : 0;

                $arr[]=[
                        'id'             => $row_id, // esto es para que no falle key de vue
                        'albaran_id'     => $albaran->id,
                        'albaran'        => $albaran->albaran,
                        'fecha'          => $albaran->fecha_albaran,
                        'serie_albaran'  => $albaran->serie_albaran,
                        'referencia'     => $albalin->producto->referencia,
                        'producto'       => $albalin->producto->nombre,
                        'clase'          => $albalin->producto->clase->nombre,
                        'precio_coste'   => $albalin->precio_coste,
                        'importe_venta'  => $albalin->importe_venta,
                        'margen'         => $margen,
                        'difrel'         => $dif_rel,
                        'efectivo'       => $cobro_alb[0],
                        'banco'          => $cobro_alb[1],
                ];

                if ($primera == true){
                    $cobro_alb = array(0,0);
                    $primera = false;
                }
            }

        }

        return $arr;

    }

    private function noFacturados($data){
        return DB::table('albaranes')
                    // ->select(DB::raw(DB::getTablePrefix().'albaranes.id,'.
                    //                 DB::getTablePrefix().'albaranes.albaran,'.
                    //                 DB::getTablePrefix().'albaranes.iva_no_residente, MAX('.
                    //                 DB::getTablePrefix().'cobros.fecha) AS fecha'))
                        ->select(DB::raw('DISTINCT '.DB::getTablePrefix().'albaranes.id'))
                        ->join('cobros', 'albaran_id', '=', 'albaranes.id')
                        ->where('albaranes.empresa_id', session('empresa')->id)
                        ->where('tipo_id', $data['tipo_id'])
                        ->where('fase_id', '<>', 10)
                        ->whereNull('factura')
                        ->where('facturar', true)
                        ->whereNull('albaranes.deleted_at')
                        ->groupBy('albaranes.id','albaran','iva_no_residente')
                        ->havingRaw('MAX('.DB::getTablePrefix().'cobros.fecha) >= ? AND MAX('.DB::getTablePrefix().'cobros.fecha) <= ?',[$data['fecha_d'],$data['fecha_h']])
                        ->orderBy('albaranes.id')
                        ->get();
    }

    private function facturados($data){
        return DB::table('albaranes')
                    ->select(DB::raw(DB::getTablePrefix().'albaranes.id,'.
                                    DB::getTablePrefix().'albaranes.albaran,'.
                                    DB::getTablePrefix().'albaranes.iva_no_residente, fecha_factura AS fecha'))
                        ->where('albaranes.empresa_id', session('empresa')->id)
                        ->where('tipo_id', $data['tipo_id'])
                        ->where('fase_id', '<>', 10)
                        ->whereDate('fecha_factura', '>=', $data['fecha_d'])
                        ->whereDate('fecha_factura', '<=', $data['fecha_h'])
                        ->whereNull('albaranes.deleted_at')
                        ->groupBy('albaranes.id','albaran','iva_no_residente', 'fecha')
                        ->orderBy('fecha')
                        ->get();
    }

    private function todos($data){
        return DB::table('albaranes')
                    // ->select(DB::raw(DB::getTablePrefix().'albaranes.id,'.
                    //                 DB::getTablePrefix().'albaranes.albaran,'.
                    //                 DB::getTablePrefix().'albaranes.iva_no_residente, MAX('.
                    //                 DB::getTablePrefix().'cobros.fecha) AS fecha'))
                        ->select(DB::raw('DISTINCT '.DB::getTablePrefix().'albaranes.id'))
                        ->join('cobros', 'albaran_id', '=', 'albaranes.id')
                        ->where('albaranes.empresa_id', session('empresa')->id)
                        ->where('tipo_id', $data['tipo_id'])
                        ->where('fase_id', '<>', 10)
                        ->where('facturar', true)
                        ->whereNull('albaranes.deleted_at')
                        ->groupBy('albaranes.id','albaran','iva_no_residente', 'fecha')
                        ->havingRaw('MAX('.DB::getTablePrefix().'cobros.fecha) >= ? AND MAX('.DB::getTablePrefix().'cobros.fecha) <= ?',[$data['fecha_d'],$data['fecha_h']])
                        ->orderBy('albaranes.id')
                        ->get();
    }

    private function detalle2($data){

        $campo_fecha = "fecha_albaran";
        if ($data['operacion'] == 'F'){
            $where = 'factura > ""';
            $campo_fecha = "fecha_factura";
        }
        elseif ($data['operacion'] == 'N')
            $where = DB::getTablePrefix().'albaranes.factura IS NULL';
        else
            $where = DB::getTablePrefix().'albaranes.id > 0';


        $select=DB::getTablePrefix().'albaranes.id AS id, fecha_albaran,albaran,serie_albaran,factura, fecha_factura,referencia,'.
                DB::getTablePrefix().'productos.nombre AS producto,'.
                DB::getTablePrefix().'clases.nombre AS clase,SUM('.DB::getTablePrefix().'albalins.precio_coste) AS precio_coste, SUM(importe_venta) AS importe_venta';

        $union0 = DB::table('albaranes')
            ->select(DB::raw($select))
            ->join('albalins','albaranes.id','=','albalins.albaran_id')
            ->join('productos','productos.id','=','albalins.producto_id')
            ->join('clases','clase_id','=','clases.id')
            ->where('albaranes.empresa_id', session('empresa')->id)
            ->where('fase_id', '<>', 10)
            ->whereNull('albaranes.deleted_at')
            ->whereDate($campo_fecha,'>=', $data['fecha_d'])
            ->whereDate($campo_fecha,'<=', $data['fecha_h'])

            // ->whereDate('albaranes.updated_at','>=', $data['fecha_d'])
            // ->whereDate('albaranes.updated_at','<=', $data['fecha_h'])
            ->where('tipo_id', $data['tipo_id'])
            ->whereRaw($where)
            ->groupBy(DB::raw('id, fecha_albaran,albaran,serie_albaran,factura,fecha_factura,referencia,producto,clase'))
            ->get();

        $row_id = 0;
        $alb_ant = -1;
        $arr=array();
        foreach ($union0 as $row){

            $row_id++;

            if ($alb_ant != $row->id){
                $cobros = Cobro::selectRaw('fpago_id, SUM(importe) AS importe')->where('albaran_id', $row->id)->groupBy('fpago_id')->get();

                $cobro_alb=array(0,0);
                foreach ($cobros as $cobro){
                    if ($cobro->fpago_id == 1)
                        $cobro_alb[0] += $cobro->importe;
                    else
                        $cobro_alb[1] += $cobro->importe;
                }
                $alb_ant = $row->id;
            }
            else{
                $cobro_alb=array(0,0);
            }

            $arr[]=[
                    'id'             =>  $row_id, // esto es para que no falle key de vue
                    'albaran_id'     => $row->id,
                    'albaran'        => $row->albaran,
                    'fecha'          => $row->fecha_albaran,
                    'serie_albaran'  => $row->serie_albaran,
                    'referencia'     => $row->referencia,
                    'producto'       => $row->producto,
                    'clase'          => $row->clase,
                    'precio_coste'   => $row->precio_coste,
                    'importe_venta'  => $row->importe_venta,
                    'margen'         => $row->importe_venta - $row->precio_coste,
                    'efectivo'       => $cobro_alb[0],
                    'banco'          => $cobro_alb[1],
            ];
        }

        return $arr;

    }

     /**
     * Recibe las facturas por request, previamente de $this->lisfac()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new DetalleVentasExport($request->data), 'file.xlsx');

    }
}
