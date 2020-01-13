<?php

namespace App\Observers;

use App\Comline;
use App\Hcomline;
use Carbon\Carbon;

class ComlineObserver
{

    public function created(Comline $comline)
    {
        $comline->setTotalesCompra($comline);
    }

    public function updated(Comline $comline)
    {
        $comline->setTotalesCompra($comline);
    }

    /**
     * Handle the comline "updated" event.
     *
     * @param  \App\Comline  $comline
     * @return void
     */
    public function updating(Comline $comline)
    {
        //$comline->setTotalesCompra($comline);

        $comline_old = Comline::find($comline->id);

        $data = $comline_old->toArray();
        $data['id']=null;
        $data['comline_id']=$comline_old->id;
        $data['operacion']='U';
        $data['username_his']=session('username');
        $data['created_his']=Carbon::now();

        Hcomline::create($data);
    }

    /**
     * Handle the comline "deleted" event.
     *
     * @param  \App\Comline  $comline
     * @return void
     */
    public function deleted(Comline $comline)
    {

        $comline->setTotalesCompra($comline);

        $data = $comline->toArray();
        $data['id']=null;
        $data['comline_id']=$comline->id;
        $data['operacion']='D';
        $data['username_his']=session('username');
        $data['created_his']=Carbon::now();

        Hcomline::create($data);
    }


}
