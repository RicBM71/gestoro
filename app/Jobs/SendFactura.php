<?php

namespace App\Jobs;

use App\Mail\Factura;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendFactura implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->data['albaran']->cliente->email)->send(new Factura($this->data));

        if ($this->data['albaran']->factura > 0)
            unlink (storage_path('albaranes/'.$this->data['albaran']->fac_ser.'.pdf'));
        else
            unlink (storage_path('albaranes/'.$this->data['albaran']->alb_ser.'.pdf'));

    }
}
