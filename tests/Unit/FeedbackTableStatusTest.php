<?php

namespace Tests\Unit;

use App\Models\Feedback;
use Carbon\Carbon;
use Tests\TestCase;

class FeedbackTableStatusTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_new_feedback_remains_new_during_its_first_24_hours(): void
    {
        Carbon::setTestNow('2026-09-16 12:00:00');

        $feedback = new Feedback([
            'status' => 'new',
        ]);
        $feedback->created_at = now()->subHours(23)->subMinutes(59);

        $this->assertSame('new', $feedback->getTableStatusKey());
        $this->assertSame('New', $feedback->getTableStatusLabel());
    }

    public function test_new_feedback_displays_as_open_after_24_hours_without_changing_stored_status(): void
    {
        Carbon::setTestNow('2026-09-16 12:00:00');

        $feedback = new Feedback([
            'status' => 'new',
        ]);
        $feedback->created_at = now()->subHours(24);

        $this->assertSame('open', $feedback->getTableStatusKey());
        $this->assertSame('Open', $feedback->getTableStatusLabel());
        $this->assertSame('new', $feedback->status);
    }

    public function test_under_review_displays_as_reviewed_without_changing_stored_status(): void
    {
        $feedback = new Feedback([
            'status' => 'under_review',
        ]);

        $this->assertSame('reviewed', $feedback->getTableStatusKey());
        $this->assertSame('Reviewed', $feedback->getTableStatusLabel());
        $this->assertSame('under_review', $feedback->status);
    }
}
