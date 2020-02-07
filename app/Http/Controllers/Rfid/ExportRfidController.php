<?php

namespace App\Http\Controllers\Rfid;

use App\Etiqueta;
use App\Producto;
use App\Recuento;
use App\Exports\RfidExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExportRfidController extends Controller
{

    public function index(){

        if (request()->wantsJson())
            return [
                'etiquetas' => Etiqueta::selImprimibles()
            ];

    }

    public function download(Request $request){

        $data = $this->validate(request(),[
            'etiqueta_id' => 'required|integer',
            'tag'         => 'required|integer',
            'perdidas'    => 'required|boolean'
        ]);

        if ($data['perdidas'])
            return $this->perdidas($data);
        else
            return $this->etiquetas($data);


    }

    private function etiquetas($data){


        $productos = Producto::with(['clase'])->whereIn('etiqueta_id', [2,3,4])->orderBy('referencia')->get()->take($data['tag']);

        $load = array();
        foreach ($productos as $row) {

            $load[]=$this->formatearLinea($row);

            $row->update(['etiqueta_id'=>5,
                          'username' => session('username')]);

        }

        if (count($productos) > 0)
            return Excel::download(new RfidExport($load), 'eti.csv');
        else
            return abort(404, 'No hay registros');

    }

    private function perdidas($data){


        $perdidas = Recuento::with(['producto.clase'])->where('rfid_id', 3)->orderBy('producto_id')->get()->take($data['tag']);

        $load = array();
        foreach ($perdidas as $row) {

            $load[]=$this->formatearLinea($row->producto);

            $i++;

        }

        if (count($perdidas) > 0)
            return Excel::download(new RfidExport($load), 'eti.csv');
        else
            return abort(404, 'No hay registros');

    }

    private function formatearLinea($producto){

        $rfid = "#!";
        $long = strlen($producto->id);
        $rfid.= str_repeat("0", 10-$long).$producto->id;

        ($producto->etiqueta_id == 4) ? $devolucion = "(Dv) " : $devolucion = "";

        $nombre_producto = trim($devolucion.$producto->nombre);
        $nombre_producto =preg_replace("[\n|\r|\n\r]", "", $nombre_producto);

        $precio_coste = (int) $producto->precio_coste;
        $long = strlen($precio_coste);

        if ($producto->etiqueta_id == 3)
            $pvp = $producto->importe_venta;
        else
            $pvp = 0;

        $precio_coste = str_repeat("0", 4-$long).$precio_coste;

        $pos = strpos(strtoupper($producto->clase->nombre), "BRI");
        if ($pos === FALSE)
            $costecod="-";
        else
            $costecod="BR".	rand(0, 99).$precios_coste.rand(0, 99);

        $clase =  ($producto->quilates > 0) ? $producto->quilates.'K' : null;

        return [
            'reg'           => '01',
            'rfid'          => $rfid,
            'check'         => null,
            'nombre'        => str_replace(';','',$nombre_producto),
            'referencia'    => $producto->referencia,
            'pvp'           => $pvp,
            'clase'         => $clase,
            'coste'         => $costecod
        ];

    }




}