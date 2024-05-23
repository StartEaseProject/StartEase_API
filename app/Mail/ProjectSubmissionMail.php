<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectSubmissionMail extends Mailable 
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    private $project;
    private $user_type;

    public function __construct($project, String $user_type)
    {
        $this->project = $project;
        $this->user_type = $user_type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Project Submission Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return (new Content())
            ->view('mails.projectSubmission')
            ->with('project', $this->project)
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
