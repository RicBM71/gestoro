<?php

namespace App\Http\Controllers\Utilidades;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\CalcularExistenciasTrait;

class CalcularExistenciasController extends Controller
{

    use CalcularExistenciasTrait;

    public function submit(){


        $this->valorDepositos('2020-12-31');

        $this->valorInventario('2020-12-31');

    }



}
