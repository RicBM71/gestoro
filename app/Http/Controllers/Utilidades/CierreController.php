<?php

namespace App\Http\Controllers\Utilidades;

use App\Caja;
use App\Libro;
use App\Empresa;
use App\Contador;
use App\Scopes\EmpresaScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CierreController extends Controller
{


    public function submit(Request $request)
    {

        if (!esRoot()){
            return abort(403, ' NO tiene permiso de acceso - Gestor');
        }

        $data = $request->validate([
            'ejercicio'     => ['required','integer'],
            'libros'        => ['required','boolean'],
            'contadores'    => ['required','boolean'],
            'saldos'        => ['required','boolean'],
        ]);


        $this->libros($data);

        $this->contadores($data);

        $this->saldos($data);

    }

    private function libros($param){

        if ($param['libros'] == false)
            return false;

        if (Libro::withOutGlobalScope(EmpresaScope::class)->where('ejercicio', $param['ejercicio'] + 1)->count() > 0)
            return false;

        $libros = Libro::withOutGlobalScope(EmpresaScope::class)
                            ->where('ejercicio', $param['ejercicio'])
                            ->where('cerrado', false)
                            ->get();

        foreach ($libros as $libro){

            $data = collect($libro)->toArray();

            $data['id'] = null;
            $data['ejercicio'] = $param['ejercicio'] + 1;
            $data['ult_compra'] = 0;
            $data['ult_factura'] = 0;
            $data['username'] = session('username');

            Libro::create($data);

        }

        DB::update('UPDATE '.DB::getTablePrefix().'libros SET cerrado = 1 WHERE ejercicio = '.$param['ejercicio']);


    }

    private function contadores($param){

        if ($param['contadores'] == false)
            return false;

        $contadores = Contador::withOutGlobalScope(EmpresaScope::class)
                            ->where('ejercicio', $param['ejercicio'])
                            ->where('cerrado', false)
                            ->get();

        foreach ($contadores as $contador){

            $data = collect($contador)->toArray();

            $data['id'] = null;
            $data['ejercicio'] = $param['ejercicio'] + 1;
            $data['ult_albaran'] = 1000;
            $data['ult_factura'] = 0;
            $data['ult_factura_auto'] = 0;
            $data['ult_factura_abono'] = 0;
            $data['username'] = session('username');

            Contador::create($data);


        }


    }

    private function saldos($param){

        if ($param['saldos'] == false)
            return false;

        $empresas = Empresa::flag(0)->get();

        $fecha_new = ($param['ejercicio']+1).'-01-01';

        $fecha_cie = ($param['ejercicio']).'-12-31';

        foreach ($empresas as $empresa){

            $saldo = Caja::saldoByEmpresa($empresa->id, $fecha_cie);

            if ($saldo == 0){
                continue;
            }

            $data = [
                'empresa_id'=> $empresa->id,
                'nombre'    => 'Apertura',
                'importe'   => $saldo,
                'fecha'     => $fecha_new,
                'dh'        => 'H',
                'manual'    => 'R',
                'username'  => session('username')
            ];

            Caja::create($data);
        }

    }





}
