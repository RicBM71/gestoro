<?php

namespace App\Http\Controllers\Exportar;

use App\Tipo;
use App\Clase;
use App\Compra;
use App\Quilate;
use App\Scopes\EmpresaScope;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Rules\MaxDiasRangoFechaRule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DetalleComprasExport;

class DetalleComprasController extends Controller
{
    public function index(){


        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $param_frm = session('frm_detacom') !=null ? session('frm_detacom') : false;

        if (request()->wantsJson())
            return [
                'tipos' => Tipo::selTiposCom(),
                'clases' => Clase::selGrupoClase(),
                'quilates' => Quilate::selQuilates(),
                'param_frm' => $param_frm
            ];

    }

    public function submit(Request $request){

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $request->validate([
            'fecha_d'   => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'   => ['required','date', new MaxDiasRangoFechaRule($request->fecha_d, $request->fecha_h)],
            'tipo_id'   => ['required','integer'],
            'clase_id'  => ['required','integer'],
            'operacion' => ['required','string'],
            'quilates'  => ['nullable','integer'],
            'concepto'  => ['nullable'],
        ]);

        session(['frm_detacom' => $data]);

        return $this->detalle($data);

    }

    private function detalle($data){

        if ($data['operacion'] == 'L')
            $where = 'fecha_liquidado > ""';
        elseif ($data['operacion'] == 'N')
            $where = DB::getTablePrefix().'compras.id not in (SELECT DISTINCT compra_id from '.DB::getTablePrefix().'productos WHERE empresa_id = '. session('empresa')->id.' AND compra_id > 0)';
        else
            $where = DB::getTablePrefix().'compras.id > 0';

        if ($data['quilates'] > 0)
            $where_k =  DB::getTablePrefix().'comlines.quilates = '.$data['quilates'];
        else
            $where_k = DB::getTablePrefix().'compras.id > 0';

        if ($data['concepto'] != null){

            // \Log::info(Compra::withOutGlobalScope(EmpresaScope::class)
            // ->select('compras.id','comlines.id AS comline_id','tipo_id','serie_com','albaran','fecha_compra','concepto','grabaciones','clases.nombre AS clase','comlines.quilates AS quilates','peso_gr','comlines.importe','fecha_liquidado')
            //     ->with(['productos'])
            //     ->join('comlines','compras.id','=','comlines.compra_id')
            //     ->join('clases','clase_id','=','clases.id')
            //     ->where('comlines.concepto', 'like', '%rolex%')->toSql());

            $union0 = Compra::withOutGlobalScope(EmpresaScope::class)
                ->select('compras.id','comlines.id AS comline_id','tipo_id','serie_com','albaran','fecha_compra','concepto','grabaciones','clases.nombre AS clase','comlines.quilates AS quilates','peso_gr','comlines.importe','fecha_liquidado','compras.empresa_id')
                    ->with(['productos'])
                    ->join('comlines','compras.id','=','comlines.compra_id')
                    ->join('clases','clase_id','=','clases.id')
                    ->whereIn('compras.empresa_id', session('empresas_usuario'))
                    ->where('tipo_id', $data['tipo_id'])
                    ->whereRaw($where)
                    ->where('comlines.concepto', 'like', '%'.$data['concepto'].'%')
                    //->whereRaw($where)
                    ->get();

        }
        else{

            $union0 = Compra::withOutGlobalScope(EmpresaScope::class)
                ->select('compras.id','comlines.id AS comline_id','tipo_id','serie_com','albaran','fecha_compra','concepto','grabaciones','clases.nombre AS clase','comlines.quilates AS quilates','peso_gr','comlines.importe','fecha_liquidado')
                    ->with(['productos'])
                    ->join('comlines','compras.id','=','comlines.compra_id')
                    ->join('clases','clase_id','=','clases.id')
                    ->where('compras.empresa_id', session('empresa')->id)
                    ->whereDate('fecha_compra','>=', $data['fecha_d'])
                    ->whereDate('fecha_compra','<=', $data['fecha_h'])
                    ->where('tipo_id', $data['tipo_id'])
                    ->where('clase_id', $data['clase_id'])
                    // ->where('comlines.concepto', 'like', '%sello%')
                    ->whereRaw($where)
                    ->whereRaw($where_k)
                    ->get();

        }

        return $union0;
    }

    // private function detalle($data){

    //     if ($data['operacion'] == 'L')
    //         $where = 'fecha_liquidado > ""';
    //     elseif ($data['operacion'] == 'N')
    //         $where = DB::getTablePrefix().'compras.id not in (SELECT DISTINCT compra_id from '.DB::getTablePrefix().'productos WHERE empresa_id = '. session('empresa')->id.' AND compra_id > 0)';
    //     else
    //         $where = DB::getTablePrefix().'compras.id > 0';

    //     $union0 = DB::table('compras')
    //         ->select('compras.id','tipo_id','serie_com','albaran','fecha_compra','concepto','grabaciones','clases.nombre AS clase','comlines.quilates AS quilates','peso_gr','comlines.importe')
    //         ->join('comlines','compras.id','=','comlines.compra_id')
    //         ->join('clases','clase_id','=','clases.id')
    //         ->where('compras.empresa_id', session('empresa')->id)
    //         ->whereDate('fecha_compra','>=', $data['fecha_d'])
    //         ->whereDate('fecha_compra','<=', $data['fecha_h'])
    //         ->where('tipo_id', $data['tipo_id'])
    //         ->where('clase_id', $data['clase_id'])
    //         ->whereRaw($where)
    //         ->get();


    //     return $union0;
    // }

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
