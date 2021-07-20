<?php

namespace App\Http\Controllers\Compras;

use App\Libro;
use App\Compra;
use App\Comline;
use App\Hcompra;
use App\Deposito;
use App\Hcomline;
use App\Hdeposito;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HcomprasController extends Controller
{

    public function index()
    {

        // if (session('empresa')->getFlag(1) == false){
        //     return abort(411, 'Esta empresa no admite compras');
        // }

        //         //->where('fecha_compra','>=',Carbon::today()->subDays(30))
        // if (request()->session()->has('filtro_com')){
        //     $data = $this->miFiltro();
        // }else{

        //     $lotes_abiertos = Compra::where('fase_id','<=',3)
        //                             ->where('username', session('username'));
        //     if ($lotes_abiertos->count() > 0)
        //         $data = $lotes_abiertos->get()->load(['cliente','grupo','tipo','fase']);
        //     else
        //         $data = Compra::with(['cliente','grupo','tipo','fase'])
        //            ->where('fecha_compra','=',Carbon::today())
        //            ->get();
        // }

       if (request()->wantsJson())
            return Hcompra::with(['cliente','grupo','tipo','fase'])->orderBy('id','desc')->get()->take(500);
    }

    public function show(Hcompra $hcompra)
    {


        $hcompra->load(['cliente','grupo','grupo','tipo','fase']);


        $grabaciones = false;
        $dias_cortesia = 7;


        if (request()->wantsJson())
            return [
                'hcompra'           => $hcompra,
                'hdepositos'        => Hdeposito::with(['concepto'])->where('compra_id',$hcompra->compra_id)->get(),
                'hcomlines'         => Hcomline::with(['clase'])->where('compra_id',$hcompra->compra_id)->get(),
                'grabaciones'       => $grabaciones,
                'dias_cortesia'     => $dias_cortesia,
            ];

    }

    public function historial($compra_id){

        if (!hasReaCom()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Reabrir');
        }


        if (request()->wantsJson())
            return Hcompra::where('compra_id', $compra_id)
                            //->where('operacion', 'I')
                            ->orderBy('created_his','desc')
                            ->get();

    }

    public function restore(Hcompra $hcompra){

        if (!esRoot()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Root');
        }

        $data = $hcompra->toArray();

        //$data['username']= 'Restaurada';
        //$data['created_at']=Carbon::now();
        //$data['updated_at']=Carbon::now();

        $compra = Compra::create($data);

        $lineas = HComline::where('compra_id', $hcompra->compra_id)->get();
        foreach ($lineas as $linea){
            $l = $linea->toArray();
            $l['compra_id'] = $compra->id;
            Comline::create($l);
        }

        $depositos = HDeposito::where('compra_id', $hcompra->compra_id)->get();
        foreach ($depositos as $deposito){
            $dep = $deposito->toArray();
            $dep['compra_id'] = $compra->id;
            Deposito::create($dep);
        }

       // $hcompra->delete();

        if (request()->wantsJson())
            return [
                'compra' => $compra
            ];

    }
}
