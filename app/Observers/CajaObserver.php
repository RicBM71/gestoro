<?php

namespace App\Observers;

use App\Caja;
use App\Hcaja;
use Carbon\Carbon;

class CajaObserver
{


    /**
     * Handle the caja "updated" event.
     *
     * @param  \App\Caja  $caja
     * @return void
     */
    public function updating(Caja $caja)
    {

       $caja_old = Caja::find($caja->id);

       $data = $caja_old->toArray();
       $data['id']=null;
       $data['caja_id']=$caja_old->id;
       $data['operacion']='U';
       $data['username_his']=session('username');
       $data['created_his']=Carbon::now();

       Hcaja::create($data);
    }

    /**
     * Handle the caja "deleted" event.
     *
     * @param  \App\Caja  $caja
     * @return void
     */
    public function deleted(Caja $caja)
    {
        $data = $caja->toArray();

        $data['id']=null;
        $data['caja_id']=$caja->id;
        $data['operacion']='D';
        $data['username_his']=session('username');
        $data['created_his']=Carbon::now();

        Hcaja::create($data);
    }


}
