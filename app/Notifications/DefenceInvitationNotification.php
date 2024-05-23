<?php

namespace App\Notifications;

use App\Mail\DefenceInvitationMail;
use App\Models\Defence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class DefenceInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $mail;
    /**
     * Create a new notification instance.
     */
    public function __construct(String $role, Defence $defence, $user_type)
    {
        $this->mail = new DefenceInvitationMail($role, $defence, $user_type);
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
