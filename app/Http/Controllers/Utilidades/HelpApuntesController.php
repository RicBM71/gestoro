<?php

namespace App\Http\Controllers\Utilidades;


use App\Apunte;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpApuntesController extends Controller
{

    public function index()
    {

        if (request()->wantsJson())
            if (esAdmin())
                return Apunte::selApuntes();
            else
                return Apunte::selApuntes();

    }
}
