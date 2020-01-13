<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Factura extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        if ($this->data['albaran']->factura > 0){
            $file = $this->data['albaran']->fac_ser.'.pdf';
            $asunto = "Factura ".$this->data['albaran']->fac_ser;
        }
        else{
            $file = $this->data['albaran']->alb_ser.'.pdf';
            $asunto = "Albarán ".$this->data['albaran']->alb_ser;
        }

        return $this->markdown('emails.factura')
                    ->with('data', $this->data)
                    ->subject($asunto)
                    ->from($this->data['from'],$this->data['razon'])
                    ->attach(storage_path('albaranes/'.$file), [
                        'as' => $file,
                        'mime' => 'application/pdf'
                ]);
    }
}
