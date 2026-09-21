<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipient,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Kopi Rider SMTP test — it works!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.test',
            with: [
                'recipient' => $this->recipient,
                'actionUrl' => url('/login'),
                'actionText' => 'Go to staff login',
            ],
        );
    }
}
