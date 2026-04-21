<?php

namespace Tests\Feature;

use App\Mail\FeedbackResponseMail;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FeedbackAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_feedback_status_and_get_toast_feedback(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'is_first_user' => true,
        ]);

        $feedback = $this->createFeedback([
            'status' => 'new',
        ]);

        $response = $this->actingAs($admin)->post(route('feedback.admin.status', $feedback), [
            'status' => 'under_review',
        ]);

        $response->assertRedirect(route('feedback.admin.show', $feedback));
        $response->assertSessionHas('toast');

        $this->assertDatabaseHas('feedback_submissions', [
            'id' => $feedback->id,
            'status' => 'under_review',
        ]);
    }

    public function test_admin_can_store_internal_comment(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'is_first_user' => true,
        ]);

        $feedback = $this->createFeedback([
            'status' => 'new',
        ]);

        $response = $this->actingAs($admin)->post(route('feedback.admin.note', $feedback), [
            'note_content' => 'We are reviewing this complaint with the service team.',
        ]);

        $response->assertRedirect(route('feedback.admin.show', $feedback));

        $this->assertDatabaseHas('internal_notes', [
            'feedback_id' => $feedback->id,
            'author_id' => $admin->id,
            'content' => 'We are reviewing this complaint with the service team.',
        ]);

        $this->assertDatabaseHas('feedback_submissions', [
            'id' => $feedback->id,
            'status' => 'under_review',
        ]);
    }

    public function test_admin_response_is_saved_emailed_and_visible_on_tracking_portal(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'is_first_user' => true,
        ]);

        $feedback = $this->createFeedback([
            'status' => 'under_review',
            'email' => 'patient@example.com',
        ]);

        $response = $this->actingAs($admin)->post(route('feedback.admin.response', $feedback), [
            'response_content' => 'Thank you for your feedback. Our team has addressed the issue and taken corrective action.',
        ]);

        $response->assertRedirect(route('feedback.admin.show', $feedback));

        $this->assertDatabaseHas('patient_responses', [
            'feedback_id' => $feedback->id,
            'sent_by' => $admin->id,
            'is_public' => true,
        ]);

        $this->assertDatabaseHas('feedback_submissions', [
            'id' => $feedback->id,
            'status' => 'responded',
        ]);

        Mail::assertSent(FeedbackResponseMail::class, function (FeedbackResponseMail $mail) use ($feedback) {
            return $mail->hasTo($feedback->email);
        });

        $trackingResponse = $this->get(route('feedback.track', ['reference_no' => $feedback->reference_no]));

        $trackingResponse->assertOk();
        $trackingResponse->assertSee('Responded');
        $trackingResponse->assertSee('Thank you for your feedback. Our team has addressed the issue and taken corrective action.');
    }

    private function createFeedback(array $overrides = []): Feedback
    {
        return Feedback::create(array_merge([
            'reference_no' => 'CCBRT-2026-00001',
            'patient_name' => 'Jane Patient',
            'email' => 'jane@example.com',
            'phone' => '0712345678',
            'service_units' => ['physician'],
            'service_category' => 'outpatient',
            'feedback_type' => 'complaint',
            'service_rating' => 'poor',
            'confidentiality_respected' => true,
            'visit_date' => '2026-04-10',
            'overall_experience' => 'The waiting time was too long and communication was unclear.',
            'improvement_suggestion' => 'Please improve queue updates.',
            'message' => 'I had to wait for several hours before being seen.',
            'is_urgent' => false,
            'consent_given' => true,
            'status' => 'new',
        ], $overrides));
    }
}
