<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WeeklyDigest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $upcomingEvents,
        public Collection $latestNews,
        public Collection $newAlumni,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ICCBI Alumni — Your Weekly Update',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-digest',
        );
    }
}
