<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectInvitationMail extends Mailable 
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    private $position;
    private $project;
    private $user_type;

    public function __construct($position, $project, $user_type)
    {
        $this->position = $position;
        $this->project = $project;
        $this->user_type = $user_type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Project Invitation Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return (new Content())
        ->view('mails.projectInvitation')
        ->with('position', $this->position)
        ->with('project', $this->project);
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
