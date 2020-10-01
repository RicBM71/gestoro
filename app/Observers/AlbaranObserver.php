<?php

namespace App\Observers;

use App\Albaran;
use App\Producto;

class AlbaranObserver
{
    protected $fase_id;

 /**
     * Handle the caja "updated" event.
     *
     * @param  \App\Albaran  $albaran
     * @return void
     */
    public function updated(Albaran $albaran)
    {

        $this->fase_id = $albaran->fase_id;



        $albaran->albalins->each(function ($item){

            if ($this->fase_id == 10)  // reservado
                $estado_id = 3;
            elseif ($this->fase_id == 11)  // vendido
                $estado_id = 4;     // a vendido
            elseif ($this->fase_id >= 12)  // abonado
                $estado_id = 2;     // en venta

            $data=[
                'estado_id'=> $estado_id,
                'username' => session('username')
            ];

            Producto::setEstadoProducto($item->producto_id, $estado_id);


            // Producto::where('id', $item->producto_id)
            //     ->where('estado_id','<>', 5) // no tocamos los genéricos
            //     ->where('stock', 1)
            //     ->update($data);


        });

    }


    /**
     * Handle the albaran "deleted" event.
     *
     * @param  \App\Albaran  $albaran
     * @return void
     */
    public function deleted(Albaran $albaran)
    {

        $albaran->albalins->each->forceDelete();
        $albaran->cobros->each->forceDelete();

    }

}
