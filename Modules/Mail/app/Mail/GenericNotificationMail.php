<?php

namespace Modules\Mail\Mail; // If it's in the Emails folder, change this to Modules\Mail\Emails

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $mailSubject;
    public string $mailTitle;
    public array $mailData;
    public string $studentName;

    public function __construct(string $subject, string $title, string $studentName, array $data)
    {
        $this->mailSubject = $subject;
        $this->mailTitle = $title;
        $this->studentName = $studentName;
        $this->mailData = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail::emails.generic_notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
