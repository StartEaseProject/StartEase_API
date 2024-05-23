<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    private $task;
    private $member;
    private $user_type;
    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, User $member, String $user_type)
    {
        $this->task = $task;
        $this->member = $member;
        $this->user_type = $user_type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Task Submission Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return (new Content())
            ->view('mails.taskSubmission')
            ->with('task', $this->task)
            ->with('member', $this->member)
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
