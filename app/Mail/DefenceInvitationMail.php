<?php

namespace App\Mail;

use App\Models\Defence;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DefenceInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    private $role;
    private $defence;
    private $user_type;

    public function __construct(String $role, Defence $defence, $user_type)
    {
        $this->role = $role;
        $this->defence = $defence;
        $this->user_type = $user_type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Defence Invitation Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return (new Content())
        ->view('mails.defenceInvitation')
        ->with('role', $this->role)
        ->with('defence', $this->defence)
            ->with('user_type', $this->user_type);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
