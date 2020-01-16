<?php

namespace App\Observers;

use App\Caja;
use App\Cobro;
use App\Cruce;
use App\Albalin;
use App\Albaran;

class CobroObserver
{
    /**
     * Handle the cobro "created" event.
     *
     * @param  \App\Cobro  $cobro
     * @return void
     */
    public function created(Cobro $cobro)
    {

            //DATOS albaran
        $albaran = Albaran::findOrFail($cobro->albaran_id);

        if ($cobro->fpago_id == 1)
            $this->crearApunteCaja($albaran, $cobro);

        $this->updateFaseAlbaran($cobro->albaran_id);
    }

    /**
     * Handle the cobro "deleted" event.
     *
     * @param  \App\Cobro  $cobro
     * @return void
     */
    public function deleted(Cobro $cobro)
    {
        //$albaran = Albaran::findOrFail($cobro->albaran_id);
            //quitamos empresa por si existen cruces de caja.
        $cobro->apuntesCajaSinEmpresa->each->delete();

        $this->updateFaseAlbaran($cobro->albaran_id);


        // $data = $cobro->toArray();
        // $data['id']=null;
        // $data['cobro_id']=$cobro->id;
        // $data['operacion']='D';
        // $data['username_his']=session('username');
        // $data['created_his']=Carbon::now();

        // Hcobro::create($data);

    }

      /**
     * Cambia fase en función de cobros y lineas de albarán
     * En Observer CobroObserver y AlbalinObserver
     *
     * @param integer $albaran_id
     * @return void
     */
    private function updateFaseAlbaran($albaran_id){

        try{
            $albaran = Albaran::findOrFail($albaran_id);
            if ($albaran->fase_id >= 13)
                return;

            $totales = Albalin::totalAlbaranByAlb($albaran_id);
            $cobros_acuenta = Cobro::getAcuentaByAlbaran($albaran_id);

            $fase_id = ($cobros_acuenta >= $totales['total'] && $totales['total'] != 0) ? 11 : 10;

            $albaran->update(['fase_id'=>$fase_id,'username'=>session('username')]);


        } catch (\Exception $e) {

        }
        // Albaran::where('id',$albaran_id)
        //         ->where('fase_id','<=',11)
        //         ->update(['fase_id'=>$fase_id,'username'=>session('username')]);


    }


    private function crearApunteCaja($albaran, $cobro){

        // CRUCE DE CAJA
        $cruce = Cruce::venta()->find($albaran->empresa_id);
        $empresa_destino = (!$cruce) ? $albaran->empresa_id :  $cruce->destino_empresa_id;

        $dh = ($cobro->importe >= 0) ? "H" : "D";

        $concepto = "A CUENTA ALBARÁN ".$albaran->alb_ser;

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
