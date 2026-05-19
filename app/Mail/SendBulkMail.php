<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendBulkMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🌟 You\'re Invited to Join AstroTring as an Astrologer! 🌟 AstroTring पर ज्योतिषी के रूप में जुड़ने का विशेष आमंत्रण!'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.astro.bulk_mail',
            with: [
                'user' => $this->user,
                'registerUrl' => 'https://astrotring.com/astro-register',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}