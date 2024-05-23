<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\VonageMessage;

class SmsNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $message;

    public function __construct($message)
    {
        $this->message = $message;

    }

    public function via($notifiable)
    {
        return ['vonage'];
    }

    public function toVonage($notifiable)
    {
        return (new VonageMessage)->content($this->message)
            ->from(env('VONAGE_SMS_FROM'))
            ;
    }
 
    
}
