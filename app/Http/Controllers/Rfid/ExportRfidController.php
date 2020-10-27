<?php

namespace App\Http\Controllers\Rfid;

use App\Etiqueta;
use App\Producto;
use App\Recuento;
use App\Exports\RfidExport;
use Illuminate\Http\Request;
use App\Exports\Etiqueta2Export;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Scopes\EmpresaProductoScope;
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
            'perdidas'    => 'required|boolean',
            'formato'     => 'required|integer',
        ]);

        if ($data['perdidas'])
            return $this->perdidas($data);
        else
            return $this->etiquetas($data);


    }

    private function etiquetas($data){


        $productos = Producto::with(['clase'])
                        ->where('estado_id', 2)
                        ->whereIn('etiqueta_id', [2,3,4])->orderBy('referencia')->get()->take($data['tag']);

        $load = array();
        foreach ($productos as $row) {

            $load[]=$this->formatearLinea($row);

            $row->update(['etiqueta_id' => 5,
                          'username'    => session('username')]);

        }

        if (count($productos) > 0)
            return $data['formato'] == 1 ? Excel::download(new RfidExport($load), 'eti.csv') : Excel::download(new Etiqueta2Export($load), 'eti.xlsx');
        else
            return abort(404, 'No hay registros');

    }

    private function perdidas($data){


        //$perdidas = Recuento::with(['producto.clase'])->where('rfid_id', 3)->orderBy('producto_id')->get();//->take($data['tag']);

        $perdidas = DB::table('recuentos')->join('productos','productos.id','=','producto_id')
                            ->where('recuentos.empresa_id', session('empresa_id'))
                            ->where('rfid_id', 3)
                            ->orderBy('producto_id')
                            ->get();

        $load = array();
        $i=0;
        foreach ($perdidas as $row) {


            // if ($row->producto == null){

            //     try {
            //         //code...
            //         $producto = Producto::withoutGlobalScope(EmpresaProductoScope::class)->withTrashed()->findOrfail($row->producto_id);
            //     } catch (\Exception $e) {
            //         \Log::info($row);
            //     }

            //     $load[]=$this->formatearLinea($producto);
            // }else{
            //     $load[]=$this->formatearLinea($row->producto);
            // }


            $load[]=$this->formatearLinea($row);

            $i++;

        }

        if ($i > 0)
            //return Excel::download(new RfidExport($load), 'eti.csv');
            return $data['formato'] == 1 ? Excel::download(new RfidExport($load), 'eti.csv') : Excel::download(new Etiqueta2Export($load), 'eti.xlsx');
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

        if ($producto->etiqueta_id == 3 || $producto->etiqueta_id == 4)
            $pvp = getDecimal($producto->precio_venta,2);
        else
            $pvp = 0;

        //if ($long <= 4)
            $precio_coste = str_repeat("0", 5-$long).$precio_coste;

        //$pos = strpos(strtoupper($producto->clase->nombre), "BRI");
        $pos = strpos(strtoupper($producto->nombre), "BRI");

        if ($pos === FALSE)
            $costecod="-";
        else
            $costecod="BR".	rand(0, 99).$precio_coste.rand(0, 99);

        if ($producto->clase_id == 1)
            $clase = $producto->quilates.'K '.getDecimal($producto->peso_gr);
        else
            $clase = getDecimal($producto->peso_gr).' gr';


        //$clase =  ($producto->quilates > 0) ? $producto->quilates.'K' : null;

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
