<?php

namespace App\Mail;

use App\Models\Escalation;
use App\Models\Feedback;
use App\Models\Hod;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EscalationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Escalation $escalation,
        public Feedback   $feedback,
        public Hod        $hod,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Action Required] Feedback Escalation – ' . $this->escalation->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.escalation',
            with: [
                'escalation'  => $this->escalation,
                'feedback'    => $this->feedback,
                'hod'         => $this->hod,
                'respondUrl'  => url('/escalations/respond/' . $this->escalation->token),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
