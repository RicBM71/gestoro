<?php

use App\Empresa;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/emp', function (Request $request) {
    $empresa = Empresa::findOrfail(1);
    return [
        'nombre' => $empresa->nombre,
        'telefono'=> $empresa->telefono1,
        'direccion'=> $empresa->direccion,
        'poblacion'=> $empresa->poblacion,
        'cpostal'=> $empresa->cpostal,
        'email'=> $empresa->email
    ];
});
