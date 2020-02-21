<?php

namespace App\Http\Controllers\Utilidades;

use App\Producto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProducto;

class ReasignarEmpresaProductoController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Producto $producto)
    {

        if (!esRoot())
            return abort(411, 'Root requerido');

        $data = $request->validate([
                    'empresa_id'=>['required','integer']
                ]);

        $data['username'] = $request->user()->username;

        $producto->update($data);

        if (request()->wantsJson())
            return [
                'data' => $data,
                'producto'=> $producto->load('clase','empresa'),
                'message' => 'EL producto ha sido modificado'
                ];
    }
}
