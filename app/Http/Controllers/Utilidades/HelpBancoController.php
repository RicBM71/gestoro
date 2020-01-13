<?php

namespace App\Http\Controllers\Utilidades;

use App\Banco;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpBancoController extends Controller
{
    public function index(Request $request)
    {


        if (request()->wantsJson())
            return [
                'bancos'=> Banco::selEntidadBic()
            ];

    }
}
