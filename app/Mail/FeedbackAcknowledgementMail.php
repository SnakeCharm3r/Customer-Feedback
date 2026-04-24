<?php

namespace App\Mail;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackAcknowledgementMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Feedback $feedback)
    {
    }

    public function build(): self
    {
        return $this->subject("Thank you for your feedback - {$this->feedback->reference_no}")
            ->view('emails.feedback-acknowledgement')
            ->with([
                'patientName' => $this->feedback->patient_name ?? 'Valued Patient',
                'referenceNo' => $this->feedback->reference_no,
                'hospitalName' => 'CCBRT Hospital',
                'appUrl'       => config('app.url'),
            ]);
    }
}
