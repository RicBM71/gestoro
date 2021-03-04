<?php

namespace App\Http\Controllers\Ventas;

use App\Albaran;
use App\Contador;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FindAlbaranesController extends Controller
{
    public function index(){

        if (request()->wantsJson())
            return [
                'contador_def' => Contador::distinct()->orderBy('ejercicio','desc')->orderBy('serie_albaran')->first() //DB::table('libros')->distinct()->first()
            ];

    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function find(Request $request)
    {

        $data = $request->validate([
            'albaran' => ['required', 'string', 'max:10'],
            'serie'   => ['required'],
            'esfactura'=> ['boolean']
        ]);

        $albaran = Albaran::withTrashed()->serieNumero($data)->get()->first();

        if (request()->wantsJson())
            return [
                'albaran'=> $albaran
            ];
    }
}
