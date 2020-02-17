<?php

namespace App\Http\Controllers\Exportar;

use App\Tipo;
use App\Clase;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DetalleComprasExport;

class DetalleComprasController extends Controller
{
    public function index(){


        if (!auth()->user()->hasRole('Gestor')){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Gestor');
        }

        if (request()->wantsJson())
            return [
                'tipos' => Tipo::selTiposCom(),
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
            'tipo_id'=> ['required','integer'],
            'clase_id'=> ['required','integer'],
            'operacion'=> ['required','string'],
        ]);

        return $this->detalle($data);

    }

    private function detalle($data){

        if ($data['operacion'] == 'L')
            $where = 'fecha_liquidado > ""';
        elseif ($data['operacion'] == 'N')
            $where = DB::getTablePrefix().'compras.id not in (SELECT DISTINCT compra_id from '.DB::getTablePrefix().'productos WHERE empresa_id = '. session('empresa')->id.' AND compra_id > 0)';
        else
            $where = DB::getTablePrefix().'compras.id > 0';


            // \Log::info(DB::table('compras')
            // ->select('compras.id','tipo_id','serie_com','albaran','fecha_compra','concepto','grabaciones','clases.nombre AS clase','comlines.quilates AS quilates','peso_gr','comlines.importe')
            // ->join('comlines','compras.id','=','comlines.compra_id')
            // ->join('clases','clase_id','=','clases.id')
            // ->where('compras.empresa_id', session('empresa')->id)
            // ->whereDate('fecha_compra','>=', $data['fecha_d'])
            // ->whereDate('fecha_compra','<=', $data['fecha_h'])
            // ->where('tipo_id', $data['tipo_id'])
            // ->where('clase_id', $data['clase_id'])
            // ->whereRaw($where)->toSql());

        $union0 = DB::table('compras')
            ->select('compras.id','tipo_id','serie_com','albaran','fecha_compra','concepto','grabaciones','clases.nombre AS clase','comlines.quilates AS quilates','peso_gr','comlines.importe')
            ->join('comlines','compras.id','=','comlines.compra_id')
            ->join('clases','clase_id','=','clases.id')
            ->where('compras.empresa_id', session('empresa')->id)
            ->whereDate('fecha_compra','>=', $data['fecha_d'])
            ->whereDate('fecha_compra','<=', $data['fecha_h'])
            ->where('tipo_id', $data['tipo_id'])
            ->where('clase_id', $data['clase_id'])
            ->whereRaw($where)
            ->get();


        return $union0;
    }

     /**
     * Recibe las facturas por request, previamente de $this->lisfac()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new DetalleComprasExport($request->data), 'resumen.xlsx');

    }

}
