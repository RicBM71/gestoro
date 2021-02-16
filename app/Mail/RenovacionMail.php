<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RenovacionMail extends Mailable
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

        $file = 'R'.$this->data['compra']->alb_ser.'.pdf';
        $asunto = "Renovación ".$this->data['compra']->alb_ser;

        return $this->markdown('emails.renovacion')
                    ->with('data', $this->data)
                    ->subject($asunto)
                    ->from($this->data['from'],$this->data['razon'])
                    ->attach(storage_path('compras/'.$file), [
                        'as' => $file,
                        'mime' => 'application/pdf'
                ]);
    }
}
