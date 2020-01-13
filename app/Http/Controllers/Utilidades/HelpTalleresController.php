<?php

namespace App\Http\Controllers\Utilidades;

use App\Albaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpTalleresController extends Controller
{
    public function ventas(Request $request)
    {

        $data = $request->validate([
            'taller_id' => ['required','integer'],
        ]);

        $data = Albaran::with(['cliente','tipo','fase'])
                ->where('taller_id','=',$data['taller_id'])
                ->get();

        if (request()->wantsJson())
            return $data;

    }
}
