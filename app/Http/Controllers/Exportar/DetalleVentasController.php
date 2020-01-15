<?php

namespace App\Http\Controllers\Exportar;

use App\Tipo;
use App\Clase;
use App\Cobro;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use Illuminate\Support\Facades\DB;
use App\Exports\DetalleVentasExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class DetalleVentasController extends Controller
{
    public function index(){


        if (!auth()->user()->hasRole('Gestor')){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Gestor');
        }

        if (request()->wantsJson())
            return [
                'tipos' => Tipo::selTiposVen(),
                'clases' => Clase::selGrupoClase()
            ];

    }

    public function submit(Request $request){

        if (!auth()->user()->hasRole('Gestor')){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Gestor');
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

        if ($data['operacion'] == 'F')
            $where = 'factura > ""';
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
            ->whereDate('albaranes.updated_at','>=', $data['fecha_d'])
            ->whereDate('albaranes.updated_at','<=', $data['fecha_h'])
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
