<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Traits\WoocommerceTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class WooUpdateProJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WoocommerceTrait;

    protected $referencia;
    protected $ecommerce_id;
    protected $estado_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($referencia=null, $ecommerce_id=null, $estado_id=null)
    {
        $this->referencia   = $referencia;
        $this->ecommerce_id = $ecommerce_id;
        $this->estado_id    = $estado_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this->woo_update_pro($this->referencia, $this->ecommerce_id, $this->estado_id);
    }
}
