<?php

namespace App\Http\Controllers\Compras;

use App\Compra;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FindComprasController extends Controller
{
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
