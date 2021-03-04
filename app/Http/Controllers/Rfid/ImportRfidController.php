<?php

namespace App\Http\Controllers\Rfid;

use App\Empresa;
use App\Recuento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Imports\RecuentoRfidImport;
use App\Http\Controllers\Controller;
use App\Imports\LocalizarRfidImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ImportRfidController extends Controller
{
    public function index(){


        if (request()->wantsJson())
            return [
                'empresas'=>Empresa::selAllEmpresas()
            ];


    }

    public function recuento(Request $request){

        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        $data = $this->validate(request(),[
            'file' => 'required|mimetypes:text/plain|max:256',
            'agregar_empresa_id' => 'nullable'
        ]);

        $empresas[] = session('empresa_id');
        if ($data['agregar_empresa_id'] != null)
            $empresas[]=$data['agregar_empresa_id'];

        $empresas_str = implode(',',$empresas);


            // importa el fichero
        Excel::import(new RecuentoRfidImport, request()->file('file'));


            // ACTUALIZO id producto
        DB::update(DB::RAW('UPDATE '.DB::getTablePrefix().'recuentos SET producto_id = (SELECT id FROM '.DB::getTablePrefix().'productos WHERE '.DB::getTablePrefix().'productos.id = rfid_producto_id) WHERE empresa_id='.session('empresa_id')));

            // ACTUALIZO Ubicación
        DB::update(DB::RAW('UPDATE '.DB::getTablePrefix().'recuentos SET destino_empresa_id = (SELECT destino_empresa_id FROM '.DB::getTablePrefix().'productos WHERE '.DB::getTablePrefix().'productos.id = rfid_producto_id) WHERE empresa_id='.session('empresa_id')));

            // ACTUALIZO estado
        DB::update(DB::RAW('UPDATE '.DB::getTablePrefix().'recuentos SET estado_id = (SELECT estado_id FROM '.DB::getTablePrefix().'productos WHERE '.DB::getTablePrefix().'productos.id = rfid_producto_id) WHERE empresa_id='.session('empresa_id')));

            // Mal ubicadas

        if ($data['agregar_empresa_id'] > 0)
            DB::update(DB::RAW('UPDATE '.DB::getTablePrefix().'recuentos SET rfid_id = 2 WHERE empresa_id='.session('empresa_id')).' AND empresa_id <> destino_empresa_id AND destino_empresa_id <>'.$data['agregar_empresa_id']);
        else
            DB::update(DB::RAW('UPDATE '.DB::getTablePrefix().'recuentos SET rfid_id = 2 WHERE empresa_id='.session('empresa_id')).' AND empresa_id <> destino_empresa_id');

            // borradas y en recuento
        DB::update(DB::RAW('UPDATE '.DB::getTablePrefix().'recuentos SET rfid_id = 4 WHERE producto_id IN (SELECT id FROM '.DB::getTablePrefix().'productos WHERE destino_empresa_id IN ('.$empresas_str.') AND deleted_at IS NOT NULL)'));

        // vendidas y en recuento
        DB::update(DB::RAW('UPDATE '.DB::getTablePrefix().'recuentos SET rfid_id = 5 WHERE producto_id IN (SELECT id FROM '.DB::getTablePrefix().'productos WHERE destino_empresa_id IN ('.$empresas_str.') AND estado_id = 4)'));

        // RESERVADAS, las separo
        DB::update(DB::RAW('UPDATE '.DB::getTablePrefix().'recuentos SET rfid_id = 6 WHERE producto_id IN (SELECT id FROM '.DB::getTablePrefix().'productos WHERE destino_empresa_id IN ('.$empresas_str.') AND estado_id = 3)'));

        // if ($data['agregar_empresa_id'] > 0){
        //     DB::table('recuentos')->where('destino_empresa_id', $data['agregar_empresa_id'])->delete();
        // }

            // cargo productos que están en tienda en pc, pero no aparecen en recuento, o sea que debería de estar o aparecer en el recuento



        $perdidas = DB::table('productos')->select('productos.*')
                        ->whereIn('destino_empresa_id', $empresas)
                        ->where('etiqueta_id', '>=', 4)
                        ->whereIn('estado_id', [1,2,3])
                        ->whereNull('deleted_at')
                        //->whereRaw('id NOT IN (SELECT producto_id FROM '.DB::getTablePrefix().'recuentos WHERE empresa_id ='. session('empresa_id').')')
                        // ->whereNotIn('id',function($query){
                        //     $query->select('producto_id')->from('recuentos')->where('empresa_id', session('empresa_id'));})
                    //    ->toSql();
                        ->get();
        //\Log::info($perdidas);

        $data = array();
        foreach ($perdidas as $producto) {

            $r = Recuento::where('producto_id', $producto->id)->get();
            if ($r->count() > 0)
                continue;

            $data[]=array(
                'empresa_id'        => session('empresa_id'),
                'fecha'             => date('Y-m-d'),
                'producto_id'       => $producto->id,
                'estado_id'         => $producto->estado_id,
                'rfid_id'           => 3,
                'username'          => session('username'),
                'updated_at'        => Carbon::now(),
                'created_at'        => Carbon::now(),
            );
        }

        DB::table('recuentos')->insert($data);

        // borro los ok

        // DB::table('recuentos')->where('empresa_id', session('empresa_id'))
        //                       ->where('rfid_id', 1)
        //                       ->delete();

       // \Log::info(session('empresa_id'));

        //  DB::table('recuentos')->where('empresa_id', session('empresa_id'))
        //                        ->whereIn('rfid_id',[1,6])
        //                        ->delete();


        return $this->status();


    }

    public function localizar(Request $request){


        $data = $this->validate(request(),[
    		'file' => 'required|mimetypes:text/plain|max:256'
        ]);

        Excel::import(new LocalizarRfidImport, request()->file('file'));

        return $this->status();

    }

    public function status(){

        return DB::table('recuentos')->select(DB::raw(DB::getTablePrefix().'rfids.nombre AS nombre, COUNT(*) AS registros'))
                    ->join('rfids', 'rfids.id', '=', 'recuentos.rfid_id')
                    ->where('empresa_id', session('empresa_id'))
                //    ->where('fecha', date('Y-m-d'))
                    ->groupBy('nombre')
                    ->get();
    }


}
