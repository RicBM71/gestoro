<?php

namespace App\Observers;

use App\Caja;
use App\Cruce;
use App\Compra;
use App\Concepto;
use App\Deposito;
use App\Hdeposito;
use Carbon\Carbon;
use App\Scopes\EmpresaScope;

class DepositoObserver
{
    /**
     * Handle the deposito "created" event.
     *
     * @param  \App\Deposito  $deposito
     * @return void
     */
    public function created(Deposito $deposito)
    {

        if (!in_array($deposito->concepto_id,[1,4,7,10,13,16]) || $deposito->importe == 0)
            return;

             //DATOS COMPRA
        $compra = Compra::findOrFail($deposito->compra_id);

            // CRUCE DE CAJA

        $cruce = Cruce::withOutGlobalScope(EmpresaScope::class)->where('empresa_id',$deposito->empresa_id)
                        ->where('comven', 'C')
                        ->first();

        $empresa_destino = (!$cruce) ? $deposito->empresa_id :  $cruce->destino_empresa_id;

            // NOMBRE CONCEPTO
        $concepto = (Concepto::find($deposito->concepto_id));

        $dh = (in_array($deposito->concepto_id,[1,13,16])) ? "D" : "H";

            //DATOS COMPRA, lo de abjo sobra no?
      //  $compra = Compra::findOrFail($deposito->compra_id);

        $concepto = "COMPRA ".$compra->albser.' '.$concepto->nombre;

        $data = [
            'empresa_id' => $empresa_destino,
            'fecha' => $deposito->fecha,
            'dh' => $dh,
            'nombre' => $concepto,
            'importe'=> $deposito->importe,
            'manual'=> 'N',
            'deposito_id' => $deposito->id,
            'cobro_id' => null,
            'username' => $deposito->username
        ];

        Caja::create($data);

    }

    /**
     * Handle the deposito "deleted" event.
     *
     * @param  \App\Deposito  $deposito
     * @return void
     */
    public function deleted(Deposito $deposito)
    {
            //quitamos empresa por si existen cruces de caja.
        $deposito->apuntesCajaSinEmpresa->each->delete();

        $data = $deposito->toArray();
        $data['id']=null;
        $data['deposito_id']=$deposito->id;
        $data['operacion']='D';
        $data['username_his']=session('username');
        $data['created_his']=Carbon::now();

        Hdeposito::create($data);

    }

}
