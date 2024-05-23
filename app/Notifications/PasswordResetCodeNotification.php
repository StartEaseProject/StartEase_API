<?php

namespace App\Notifications;

use App\Mail\PasswordResetCodeMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PasswordResetCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $mail;
    private $code;
    /**
     * Create a new notification instance.
     */
    public function __construct($code)
    {
        $this->mail = new PasswordResetCodeMail($code);
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
