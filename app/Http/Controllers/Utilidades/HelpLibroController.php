<?php

namespace App\Http\Controllers\Utilidades;

use App\Libro;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpLibroController extends Controller
{

    public function index(Request $request)
    {
        if (request()->wantsJson())
            return [
                'libros'=> Libro::selLibros($request->ejercicio,$request->grupo_id)
            ];

    }

    public function ejercicio(Request $request)
    {
        if (request()->wantsJson())
            return [
                'libros'=> Libro::selLibrosByEjercicio($request->ejercicio)
            ];

    }

    public function abiertos()
    {
        if (request()->wantsJson())
            return [
                'libros'=> Libro::selLibrosAbiertos()
            ];

    }

}
