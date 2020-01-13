<?php

namespace App\Http\Controllers\Ventas;

use App\Albaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FindAlbaranesController extends Controller
{
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


        if (request()->wantsJson())
            return [
                'albaran'=> Albaran::serieNumero($data)->get()->first()
            ];
    }
}
