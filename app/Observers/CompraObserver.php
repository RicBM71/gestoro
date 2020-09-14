<?php

namespace App\Observers;

use App\Compra;
use App\Hcompra;
use Carbon\Carbon;

class CompraObserver
{

     /**
     * Handle the comline "updated" event.
     *
     * @param  \App\Compra  $comline
     * @return void
     */
    // public function updating(Compra $compra)
    // {

    //     $compra_old = Compra::find($compra->id);

    //     $data = $compra_old->toArray();
    //     $data['id']=null;
    //     $data['compra_id']=$compra_old->id;
    //     $data['operacion']='U';
    //     $data['username_his']=session('username');
    //     $data['created_his']=Carbon::now();

    //     Hcompra::create($data);
    // }

    /**
     * Handle the compra "deleted" event.
     *
     * @param  \App\Compra  $compra
     * @return void
     */
    public function deleted(Compra $compra)
    {

        $compra->comlines->each->delete();
        $compra->depositos->each->delete();

        $data = $compra->toArray();
        $data['id']=null;
        $data['compra_id']=$compra->id;
        $data['operacion']='D';
        $data['username_his']=session('username');
        $data['created_his']=Carbon::now();

        Hcompra::create($data);


    }


}
