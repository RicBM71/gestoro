<?php

namespace App\Http\Controllers\Utilidades;

use App\Compra;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IntercambioController extends Controller
{
    public function index($ejercicio=0){

        if (!hasReaCom()){
            return abort(403,auth()->user()->name.' NO tiene permiso de administrador');
        }



    }

    public function submit(Request $request){

        if (!hasReaCom()){
            return abort(403,auth()->user()->name.' NO tiene permiso de administrador');
        }

        $data = $request->validate([
            'serie'         => ['required','max:1'],
            'albaran'       => ['required','integer'],
            'albaran_des'   => ['required','integer'],
            'ejercicio'     => ['required','integer','min:2020'],
        ]);

        $origen = Compra::where('albaran', $data['albaran'])
                        ->where('serie_com', $data['serie'])
                        ->where('ejercicio', $data['ejercicio'])
                        ->firstOrFail();

        $destino = Compra::where('albaran', $data['albaran_des'])
                        ->where('serie_com', $data['serie'])
                        ->where('ejercicio', $data['ejercicio'])
                        ->firstOrFail();

        $data_u['username']  = $request->user()->username;
        $data_u['serie_com'] = "$";
        $data_u['albaran']   = 9999;

        $destino->update($data_u);

        $data_u['serie_com'] = $data['serie'];    //49
        $data_u['albaran']   = $data['albaran_des'];

        $origen->update($data_u);

        $data_u['albaran'] = $data['albaran']; //50
        $data_u['serie_com']   = $data['serie'];  //50
        $destino->update($data_u);

        if (request()->wantsJson())
            return $destino;


    }
}
