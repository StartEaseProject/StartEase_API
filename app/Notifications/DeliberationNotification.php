<?php

namespace App\Notifications;

use App\Mail\DeliberationMail;
use App\Models\Defence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class DeliberationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $mail;
    /**
     * Create a new notification instance.
     */
    public function __construct(Defence $defence, $user_type)
    {
        $this->mail = new DeliberationMail($defence, $user_type);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): Mailable
    {
        return $this->mail->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
