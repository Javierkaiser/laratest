<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
    
    public function toMail($notifiable)
    {
        $url = url(config('app.client_url').'/password/reset/'.$this->token)
            .'?email='.\urldecode($notifiable->email);
        return (new MailMessage)
                    ->line('Estas recibiendo esto mail porque recibimos un pedido de reseteo de password.')
                    ->action('Reset Password', $url)
                    ->line('Si no pediste cambio de contraseña, ignora esto.');
    }

}
