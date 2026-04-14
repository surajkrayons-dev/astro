<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AstroRegistrationNotification extends Mailable
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
            subject: 'New Astrologer Registration Request'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.astro.registration',
            with: [
                'name'                     => $this->user->name,
                'email'                    => $this->user->email,
                'username'                 => $this->user->username,
                'mobile'                   => $this->user->mobile,
                'country_code'             => $this->user->country_code,
                'category'                 => $this->user->category,
                'experience'               => $this->user->experience,
                'expertise'                => $this->user->expertise,
                'qualification'            => $this->user->astro_education,
                'languages'                => $this->user->languages,
                'daily_available_hours'    => $this->user->daily_available_hours,
                'is_family_astrologer'     => $this->user->is_family_astrologer,
                'family_astrology_details' => $this->user->family_astrology_details,
                'adminPanelUrl' => url('/admin/astrologers'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}