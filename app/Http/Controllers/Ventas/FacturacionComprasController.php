<?php

namespace App\Http\Controllers\Ventas;

use App\Iva;
use App\Grupo;
use App\Libro;
use App\Compra;
use App\Comline;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\FacturasExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\Albaranes\RangoFechaFacturacionRule;

/**
 * Llamado desde ComprasFac.vue
 */

class FacturacionComprasController extends Controller
{


    public function index()
    {
        if (!session('empresa')->getFlag(1)){
            return abort(403,"No se permiten compras. Contactar administrador");
        }


        if (request()->wantsJson())
            return [
                'grupos'=> Grupo::selGruposEmpresa()
            ];
    }

    /**
     *
     * Facturación de compras: recuperaciones. Recalcula importes venta en líneas.
     *
     */
    public function compras(Request $request){

        if (!session('empresa')->getFlag(1)){
            return abort(403,"No se permiten compras. Contactar administrador");
        }

        $data=$request->validate([
            'grupo_id'  => ['required','integer'],
            'fecha_d'  => ['required','date', new RangoFechaFacturacionRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
            'accion'    => ['required','string'],
        ]);

    //    $rango = trimestre($data['ejercicio'],$data['trimestre']);

        //      FACTURACIÓN
        if ($data['accion']=='F'){
            // TODO: revisar con cambio ejercicio.

            $libro = Libro::getContadorCompra(getEjercicio($data['fecha_d']),$data['grupo_id']);

            //if ($libro->ejercicio != getEjercicio())

            $facturas = $this->facturarCompras($data['fecha_d'],$data['fecha_h'],$libro);
        }
        else{       // DESFACTURAR.
            $facturas = $this->desfacturarCompras($data['fecha_d'],$data['fecha_h']);
        }

        if (request()->wantsJson())
            return ($facturas == 0) ? abort(404,'No se han encontrado facturas') : 'Procesadas '.$facturas.' facturas';

    }

    /**
     * Desfacturar
     *
     * @param [date] $d
     * @param [date] $h
     * @return integer $reg
     */
    private function desfacturarCompras($d, $h){

        $data=[
            'fecha_factura'=>null,
            'factura'=>null,
            'serie_fac'=>null,
            'username'=>session('username'),
            'updated_at'=> Carbon::now()
        ];

        $reg = Compra::where('empresa_id',session('empresa')->id)
                ->whereDate('fecha_factura','>=', $d)
                ->whereDate('fecha_factura','<=', $h)
                ->where('fase_id', 5)
                ->update($data);


        return $reg;

    }

    /**
     * @param $d date desde
     * @param $h date hasta
     * @param $libro Object Libro
     *
     */
    private function facturarCompras($d,$h,$libro){

        $i=0;

        $compras = $this->comprasRecuperadasSinFacturar($d, $h, $libro->grupo_id);

        $iva = Iva::findOrFail(2); // rebu.

        $data['username'] = session('username');

        foreach ($compras as $row){

            $i++;

            $incremento = round($row->importe_acuenta * 100 / $row->importe, 2) - 100;
            $comlines = Comline::where('compra_id', $row->id)->get();

            // recalcula importe venta con importe de recuperación, lo reparte en líneas.
            foreach ($comlines as $comline){
                $pvp = $comline->importe + round($comline->importe * $incremento / 100, 2);
                $data['iva']=$iva->importe;
                $data['importe_venta']=$pvp;

                $comline->update($data);
            }


            $libro->ult_factura = $libro->ult_factura + 1;

            $data_com = [
                'username'     => session('username'),
                'serie_fac'    => $libro->serie_fac,
                'factura'      => $libro->ult_factura,
                'fecha_factura'=> $row->fecha
            ];

            // actualiza número y fecha factura
            $compra = Compra::find($row->id);
            $compra->update($data_com);
        }

        // actualiza el contador de facturas de recuperaciones.
        $libro->update(['factura'=>$libro->ult_factura]);

        return $i;
    }

     /**
     * Seleccciona las compras que han sido recuperadas y no están facturadas
     *
     * @param date $d
     * @param date $h
     *
     */
    private function comprasRecuperadasSinFacturar($d, $h, $grupo_id){

        return DB::table('compras')
            ->join('depositos', 'compras.id', '=', 'depositos.compra_id')
            ->select('compras.*','depositos.fecha')
                ->where('compras.empresa_id',session('empresa')->id)
                ->where('compras.grupo_id', $grupo_id)
                ->whereDate('fecha','>=', $d)
                ->whereDate('fecha','<=', $h)
                ->where('fecha_factura',null)
                ->where('fase_id', 5)
                ->whereIn('concepto_id',[10,11,12])
            ->orderBy(('fecha'))
            ->get();

    }


}
