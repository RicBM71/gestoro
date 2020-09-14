<?php

namespace App\Http\Controllers\Compras;

use App\Libro;
use App\Compra;
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
}
