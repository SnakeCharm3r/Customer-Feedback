<?php

namespace App\Mail;

use App\Models\Feedback;
use App\Models\PatientResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackResponseMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Feedback $feedback,
        public PatientResponse $response
    ) {
    }

    public function build(): self
    {
        return $this->subject('Update on your feedback - ' . $this->feedback->reference_no)
            ->view('emails.feedback-response')
            ->with([
                'patientName' => $this->feedback->patient_name ?? 'Valued Patient',
                'referenceNo' => $this->feedback->reference_no,
                'statusLabel' => $this->feedback->getStatusLabel(),
                'responseContent' => $this->response->content,
                'hospitalName' => 'CCBRT Hospital',
                'trackUrl' => config('app.url') . '/track?reference_no=' . urlencode($this->feedback->reference_no),
            ]);
    }
}
