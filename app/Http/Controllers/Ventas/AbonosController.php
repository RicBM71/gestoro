<?php

namespace App\Http\Controllers\Ventas;

use App\Caja;
use App\Cobro;
use App\Cruce;
use App\Albalin;
use App\Albaran;
use App\Contador;
use App\Producto;
use Carbon\Carbon;
use App\Scopes\EmpresaScope;
use Illuminate\Http\Request;
use App\Jobs\WooUpdateProJob;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AbonosController extends Controller
{
    public function abonar(Request $request, Albaran $albarane){


        if (!hasAbono()){
            return abort(403,"No dispone de los permisos necesarios");
        }

        $albaran_new = $this->albaran($albarane, $request);

        $this->albalin($albarane, $albaran_new, false);

        $this->cobros($albarane, $albaran_new);

        if (request()->wantsJson())
            return [
                'albaran_id' => $albaran_new->id,
            ];


    }

    /**
     * Cuando cancelamos albarán, no generamos apuntes de cobro y tampoco facturamos.
     *
     * @param Request $request
     * @param Albaran $albarane
     * @return boolean
     */
    public function cancelar(Request $request, Albaran $albarane){

        if (!hasAbono()){
            return abort(403,"No dispone de los permisos necesarios");
        }

        if ($albarane->factura > 0){
            return abort(404,'El albarán está facturado, abonar en vez de cancelar!');
        }

        $albaran_new = $this->albaran($albarane, $request, true);

        $this->albalin($albarane, $albaran_new, true);

        if (request()->wantsJson())
            return [
                'albaran_id' => $albaran_new->id,
            ];


    }


    private function albaran($albarane, $request, $cancelacion=false){

        $data_new['tipo_id']        = $albarane->tipo_id;
        $data_new['fecha_albaran']  = Carbon::today();

        $data_new['empresa_id']             = $albarane->empresa_id;
        $data_new['procedencia_empresa_id']  = $albarane->procedencia_empresa_id;

        $data_new['cliente_id']             = $albarane->cliente_id;

        $data_new['fase_id']                 = 13;
        $data_new['online']                  = $albarane->online;
        $data_new['iva_no_residente']        = $albarane->iva_no_residente;
        $data_new['username']                = session('username');

        $ejercicio   = getEjercicio(Carbon::today());
        $contador_alb = Contador::incrementaContador($ejercicio, $albarane->tipo_id);
        $data_new['serie_albaran']    = $contador_alb['serie_albaran'];
        $data_new['albaran']      = $contador_alb['ult_albaran'];
        $data_new['albaran_abonado_id'] = $albarane->id;
        $data_new['motivo_id'] = $request->get('motivo_id');

        $data_new['facturar'] = $albarane->facturar;
        $data_new['fpago_id'] = $albarane->fpago_id;
        $data_new['cuenta_id'] = $albarane->cuenta_id;
        $data_new['taller_id'] = $albarane->taller_id;

        if ($albarane->factura > 0 || $cancelacion === true || session('empresa')->getFlag(6) == true){
            // actualiza el albaran de origen, ya estaba facturado.
            if ($cancelacion === true)
                $data['fase_id']  = 14;
            else
                $data['fase_id']  = 12;
            $data['motivo_id'] = $request->get('motivo_id');
            $data['username']   = session('username');
           // $albarane->update($data);
        }else{

                // facturo el albarán de origen.
            $contador_fac = Contador::incrementaFactura(getEjercicio(Carbon::today()), $albarane->tipo_id,1);
            $data['factura']       = $contador_fac['ult_factura'];
            $data['fecha_factura'] = Carbon::today();
            $data['serie_factura'] = $contador_fac['serie_factura'];
            $data['tipo_factura']  = 1;
            $data['factura_txt']   = getEjercicio(Carbon::today()).$contador_fac['serie_factura'].$contador_fac['ult_factura'];
            $data['motivo_id'] = $request->get('motivo_id');

            $data['fase_id']      = 12;
            $data['username']     = session('username');
           // $albarane->update($data); lo hago cuando creo el nuevo para hacer solo un update.
        }

        if ($cancelacion === false && session('empresa')->getFlag(6) == false){
            $contador_fac = Contador::incrementaFactura(getEjercicio(Carbon::today()), $albarane->tipo_id,3);
            $data_new['factura']       = $contador_fac['ult_factura'];
            $data_new['fecha_factura'] = Carbon::today();
            $data_new['serie_factura'] = $contador_fac['serie_factura'];
            $data_new['tipo_factura']  = 3;
            $data_new['factura_txt']   = getEjercicio(Carbon::today()).$contador_fac['serie_factura'].$contador_fac['ult_factura'];
        }

        // crea el nuevo albarán
        try{

            $albaran_new = Albaran::create($data_new);

            // Actualiza ahora el albarán para crear relacion
            //$albarane->update(['albaran_abonado_id'=>$albaran_new->id]);
            $data['albaran_abonado_id'] = $albaran_new->id;
            $albarane->update($data);

            return $albaran_new;
        }catch(\Exception $e){
            return abort(409, 'Error al insertar! '.$e->getMessage());
        }


    }

    private function albalin($albaran, $albaran_new, $cancelacion){

        $lineas = Albalin::AlbaranId($albaran->id)->get();

        foreach ($lineas as $albalin){

            $albalin_new['albaran_id']      = $albaran_new->id;
            $albalin_new['empresa_id']      = $albaran_new->empresa_id;
            $albalin_new['producto_id']     = $albalin->producto_id;
            $albalin_new['unidades']        = $albalin->unidades * -1;
            $albalin_new['importe_unidad']  = $albalin->importe_unidad;
            $albalin_new['precio_coste']    = $albalin->precio_coste * -1;
            $albalin_new['importe_venta']   = $albalin->importe_venta * -1;
            $albalin_new['iva_id']          = $albalin->iva_id;
            $albalin_new['iva']             = $albalin->iva;
            $albalin_new['username']        = $albaran_new->username;
            $albalin_new['updated_at']      = Carbon::now();
            $albalin_new['created_at']      = Carbon::now();

            //Albalin::create($albalin_new);
            //No me interesa que pase por el observer
            DB::table('albalins')->insert($albalin_new);

            $data = ['estado_id'   => 2,
                    //'etiqueta_id'  => 5,
                    'username'    => $albaran_new->username];

            if ($cancelacion === false && $albaran_new->tipo_id == 3){ // es rebu
                $data['etiqueta_id'] = 4;
            }

            Producto::where('id',$albalin->producto_id)
                    ->where('estado_id','<>', 5)
                    ->update($data);

            // $producto = $albalin->load('producto');

            // if (config('cron.woo_url') != false && $producto->online == true)
            //     dispatch(new WooUpdateProJob($producto->referencia, $producto->ecommerce_id, $data['estado_id']));

        }
    }

    private function cobros($albaran, $albaran_new){

        $lineas = Cobro::AlbaranId($albaran->id)->get();

        foreach ($lineas as $cobro){

            $cobro_new['fecha']      = Carbon::today();
            $cobro_new['albaran_id'] = $albaran_new->id;
            $cobro_new['empresa_id'] = $cobro->empresa_id;
            $cobro_new['cliente_id'] = $cobro->cliente_id;
            $cobro_new['fpago_id']   = $cobro->fpago_id;
            $cobro_new['importe']    = $cobro->importe * -1;
            $cobro_new['notas']      = null;
            $cobro_new['username']   = $albaran_new->username;
            $cobro_new['updated_at'] = Carbon::now();
            $cobro_new['created_at'] = Carbon::now();

            //no me interesa que pase por el observer
            //Cobro::create($cobro_new);
            $cobro_id = DB::table('cobros')->insertGetId($cobro_new);

            if ($cobro->fpago_id == 1)
                $this->crearApunteCaja($albaran_new, $cobro_id);

        }

    }

    private function crearApunteCaja($albaran, $cobro_id){

        $cobro = Cobro::findOrFail($cobro_id);

        // CRUCE DE CAJA
        $cruce = Cruce::withOutGlobalScope(EmpresaScope::class)->where('empresa_id',$albaran->empresa_id)
                        ->where('comven', 'V')
                        ->first();

        $empresa_destino = (!$cruce) ? $albaran->empresa_id :  $cruce->destino_empresa_id;

        if ($albaran->fase_id == 13){
            $concepto = "ABONO ALBARÁN ".$albaran->alb_ser;
            $cobro->importe = $cobro->importe * -1;
            $dh = 'D';
        }else{
            $dh = ($cobro->importe >= 0) ? "H" : "D";
            $concepto = "A CUENTA ALBARÁN ".$albaran->alb_ser;
        }



        $data = [
            'empresa_id' => $empresa_destino,
            'fecha' => $cobro->fecha,
            'dh' => $dh,
            'nombre' => $concepto,
            'importe'=> $cobro->importe,
            'manual'=> 'N',
            'cobro_id' => $cobro->id,
            'deposito_id' => null,
            'username' => $cobro->username
        ];

        Caja::create($data);


    }
}
