<?php

namespace App\Observers;

use App\Cobro;
use App\Albalin;
use App\Albaran;
use App\Producto;

class AlbalinObserver
{
    /**
     * Handle the albalin "created" event.
     *
     * @param  \App\Albalin  $albalin
     * @return void
     */
    public function created(Albalin $albalin)
    {

        //Producto::setEstadoProducto($albalin->producto_id, 3);

        $producto = Producto::withoutGlobalScope(EmpresaProductoScope::class)->findOrFail($albalin->producto_id);
        $producto->setEstadoProducto($producto, 3);

        $this->updateFaseAlbaran($albalin->albaran_id);

    }

    /**
     * Handle the albalin "updated" event.
     *
     * @param  \App\Albalin  $albalin
     * @return void
     */
    public function updated(Albalin $albalin)
    {
        $this->updateFaseAlbaran($albalin->albaran_id);
    }

    /**
     * Handle the albalin "deleted" event.
     *
     * @param  \App\Albalin  $albalin
     * @return void
     */
    public function deleted(Albalin $albalin)
    {


        $producto = Producto::withoutGlobalScope(EmpresaProductoScope::class)->findOrFail($albalin->producto_id);
        $producto->setEstadoProducto($producto, 2);

        // $data=[
        //     'estado_id'=> 2,
        //     'username' => session('username')
        // ];


        // Producto::where('id', $albalin->producto_id)
        //         ->where('estado_id','<>', 5) // no tocamos los genéricos
        //         ->where('stock', 1)
        //         ->update($data);

        $this->updateFaseAlbaran($albalin->albaran_id);
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

            $albaran->update(['fase_id' => $fase_id,
                              'online'  => false,
                              'username'=> session('username')]);

        } catch (\Exception $e) {

        }


        // Albaran::where('id',$albaran_id)
        //         ->where('fase_id','<=',11)
        //         ->update(['fase_id'=>$fase_id,'username'=>session('username')]);



    }


}
