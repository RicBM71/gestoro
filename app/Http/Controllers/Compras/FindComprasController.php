<?php

namespace App\Http\Controllers\Compras;

use App\Libro;
use App\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FindComprasController extends Controller
{

    public function index(){

        if (session('empresa')->getFlag(1) == false){
            return abort(411, 'Esta empresa no admite compras');
        }

        if (request()->wantsJson())
            return [
                'serie' => Libro::distinct()
                                ->orderBy('ejercicio','desc')
                                ->orderBy('serie_com','asc')
                                ->first() //DB::table('libros')->distinct()->first()
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

        //$this->authorize('create', Compra::class);

        $data = $request->validate([
            'albaran' => ['required', 'string', 'max:10'],
            'serie'   => ['required'],
            'esfactura'=> ['boolean']
        ]);


        if (request()->wantsJson())
            return [
                'compra'=> Compra::serieNumero($data)->get()->first()
            ];
    }
}
