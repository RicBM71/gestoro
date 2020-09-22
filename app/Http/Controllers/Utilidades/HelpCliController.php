<?php

namespace App\Http\Controllers\Utilidades;

use App\Clidoc;
use App\Compra;
use App\Albaran;
use App\Cliente;
use App\Rules\ValidarDniCif;
use App\Scopes\EmpresaScope;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpCliController extends Controller
{

    public function index(Request $request)
    {
        $data = $request->validate([
            'dni' => ['required',new ValidarDniCif($request->tipodoc)],
            'tipodoc'=> ['required']
        ]);

        $cliente = Cliente::where('dni', '=', $data['dni'])->firstOrFail();

        $docu = Clidoc::getDocumentos($cliente->id,$cliente->fecha_dni);

        if (request()->wantsJson())
            return [
                'cliente'=> $cliente,
                'documentos'=> $docu,
            ];

    }

    public function blacklist(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required'],
            'apellidos'=> ['required'],
            'bloqueado'=> ['required']
        ]);

        $cliente = Cliente::
            where('nombre', '=', $data['nombre'])->
            where('apellidos', '=', $data['apellidos'])->
            where('bloqueado','L')->
            firstOrFail();

        if (request()->wantsJson())
            return [
                'blacklist'=> true
            ];

    }

    public function compras(Request $request)
    {

        $data = $request->validate([
            'cliente_id' => ['required','integer'],
        ]);

        $data = Compra::withoutGlobalScope(EmpresaScope::class)->with(['grupo','tipo','fase','empresa'])
                ->whereIn('empresa_id', session('empresas_usuario'))
                ->where('cliente_id','=',$data['cliente_id'])
                ->get();

        if (request()->wantsJson())
            return $data;

    }

    public function ventas(Request $request)
    {

        $data = $request->validate([
            'cliente_id' => ['required','integer'],
            'ejercicio' => ['required','integer'],
        ]);

        $data = Albaran::withoutGlobalScope(EmpresaScope::class)->with(['tipo','fase','empresa'])
                ->tipo(null)
                ->whereIn('empresa_id', session('empresas_usuario'))
                ->whereYear('fecha_albaran', '>=',$data['ejercicio'] - 1)
                ->where('cliente_id','=',$data['cliente_id'])
                ->orderBy('albaranes.id','desc')
                ->get() ;

        if (request()->wantsJson())
            return $data;

    }

    public function dni(Request $request)
    {
        $data = $request->validate([
            'dni' => ['required','alpha_num','min:4'],
        ]);

        try{
            return Cliente::where('dni', '=', $data['dni'])->FirstOrFail();

        } catch (\Exception $e) {
            return abort(404, 'Cliente no existe! ');
        }

    }



}
