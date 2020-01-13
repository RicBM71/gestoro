<?php

namespace App\Observers;

use App\Compra;

class CompraObserver
{


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


    }


}
