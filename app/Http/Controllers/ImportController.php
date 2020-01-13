<?php

namespace App\Http\Controllers;

use App\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function index(){
        $reg = DB::connection('quilates')->select('select * from empresas');

        foreach ($reg as $row){
            $data[]=array(
                'id'        => $row->id,
                'nombre'    => $row->nombre,
                'razon'     => $row->razon,
                'cif'=> $row->cif,
                'poblacion'=> $row->poblacion,
                'direccion'=> $row->direccion,
                'cpostal'=> $row->cpostal,
                'provincia'=> $row->provincia,
                'telefono1'=> $row->telefono,
                'telefono2'=> $row->fax,
                'contacto'=> $row->contacto,
                'email'=> $row->email,
                'web'=> $row->web,
                'txtpie1'=> $row->txtpie1,
                'txtpie2'=> $row->txtpie2,
                'flags'=> $row->flags,
                'sigla'=> $row->sigla,
                'titulo'=> $row->titulo,
                'logo'=> $row->logo,
                'almacen_id'=> $row->id,
                'username'=> $row->sysusr
            );
        }

        Empresa::insert($data);

        dd($data);
    }
}
