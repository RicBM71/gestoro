<?php

namespace App\Http\Controllers\Utilidades;

use App\Compra;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CambioInteresController extends Controller
{
    public function submit(Request $request)
    {

        if (!esRoot()){
            return abort(403, ' NO tiene permiso de acceso - root');
        }

        $data = $request->validate([
            'interes'     => ['required','numeric'],
        ]);

        $compras = Compra::where('tipo_id',1)
                            ->where('fase_id', 4)
                            ->where('interes', 5)
                            ->get();

        $i=0;
        foreach ($compras as $compra)                            {

            // if ($compra->interes < $data['interes'])
            //     continue;

            $data_com['username'] = "Gerencia";
            $data_com['importe_renovacion']   = round(($compra->importe - $compra->importe_acuenta)  *  $data['interes'] / 100, 0);
            $data_com['importe_recuperacion'] = $data_com['importe_renovacion'];

            $data_com['interes'] = $data['interes'];
            $data_com['interes_recuperacion'] = $data['interes'];

            $compra->update($data_com);

            $i++;

        }



        if (request()->wantsJson())
            return [
                'registros' => $i
            ];


    }
}
