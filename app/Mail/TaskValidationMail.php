<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskValidationMail extends Mailable
{
    use Queueable, SerializesModels;

    private $task;
    private $accepted;
    private $user_type;
    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, bool $accepted, String $user_type)
    {
        $this->task = $task;
        $this->accepted = $accepted;
        $this->user_type = $user_type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Task Validation Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return (new Content())
            ->view('mails.taskValidation')
            ->with('task', $this->task)
            ->with('accepted', $this->accepted)
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
