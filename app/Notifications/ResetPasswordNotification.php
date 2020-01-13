<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    //
    use Queueable;

    public $token;
    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
      //  $this->data = session()->all();

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {


        return (new MailMessage)
            ->greeting('Hola '.$notifiable->name.': ')
//                ->with('data', $this->data)
                ->from(config('mail.username'),config('app.name'))
                ->subject('Solicitud reset Contraseña')
                ->line('Has recibido este email porque se ha solicitado un reseteo de la contraseña asociada a tu cuenta.')
                ->line('Usuario: '.$notifiable->username)
                ->action('Reset Password', url(config('app.url').route('password.reset', $this->token, false)))
                ->line('Si no has realizado esta petición puedes ignorar este email. Este link es válido durante 60 minutos.')
                ->salutation('¡Saludos!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
