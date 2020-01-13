<?php

namespace App\Http\Controllers\Ventas;

use App\Iva;
use App\Grupo;
use App\Libro;
use App\Compra;
use App\Comline;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use App\Exports\FacturasExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

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
                'grupos'=> Grupo::selGruposRebu()
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
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
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
            'factura'=>0,
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

        $compras = Compra::comprasRecuperadasSinFacturar($d, $h, $libro->grupo_id);

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

            $data_com = [
                'username' => session('username'),
                'serie_fac'=> $libro->serie_fac,
                'factura'=> $libro->ult_factura++,
                'fecha_factura'=> $row->fecha
            ];

            // actualiza número y fecha factura
            $compra = Compra::find($row->id);
            $compra->update($data_com);
        }

        // actualiza el contador de facturas de recuperaciones.
        $libro->update(['factura'=>$libro->factura]);

        return $i;
    }

     /**
     *
     * Relación de facturas de recuperaciones.
     *
     */
    public function lisrecu(Request $request){

        $data=$request->validate([
            'grupo_id'  => ['required','integer'],
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
        ]);

        //$rango = trimestre($data['ejercicio'],$data['trimestre']);

        $facturas = Compra::with(['cliente','comlines'])
                        ->grupo($data['grupo_id'])
                        ->fecha($data['fecha_d'], $data['fecha_h'], "F")
                        ->where('factura','>', 0)
                        ->orderBy('factura')
                        ->get();

        $collection=[];
        foreach ($facturas as $row){

            $pvp = $coste = $bene = 0;

            foreach($row->comlines as $li){
                $pvp+= $li->importe_venta;
                $coste+= $li->importe;
                $bene = $pvp - $coste;
            }

            // como no hay más de dos tipos de iva, puedo 'atajar' así:

            $base = round($bene / (1+($li->iva/100)),2);
            $iva = round($bene - $base,2);

            $collection[]=[
                'facser'         => $row->serie_fac.'-'.$row->factura,
                'factura'        => $row->factura,
                'fecha_factura'  => Carbon::parse($row->fecha_factura)->format('Y-m-d'),
                'fecha_compra'   => Carbon::parse($row->fecha_compra)->format('Y-m-d'),
                'alb_ser'        => $row->alb_ser,
                'dni'            => $row->cliente->dni,
                'razon'          => $row->cliente->razon,
                'pvp'            => round($pvp,2),
                'coste'          => round($coste),
                'bene'           => round($bene),
                'base'           => $base,
                'iva'            => $iva,
                'tipo_id'        => $row->tipo_id,
                'por_iva'        => $li->iva,
                'rebu'           => 'REBU',
                'id'             => $row->id
            ];

        }
        return collect($collection);

    }

    /**
     * Recibe las facturas por request, previamente de $this->lisrecu()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new FacturasExport($request->data), 'fac.xlsx');

    }
}
