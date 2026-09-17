<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackCollectionMeansTest extends TestCase
{
    use RefreshDatabase;

    public function test_collection_means_expose_social_media_and_sms_but_not_paper_form(): void
    {
        $this->assertSame('Social Media', Feedback::COLLECTION_MEANS['social_media'] ?? null);
        $this->assertSame('SMS', Feedback::COLLECTION_MEANS['sms'] ?? null);
        $this->assertArrayNotHasKey('paper_form', Feedback::COLLECTION_MEANS);
        $this->assertArrayNotHasKey('paper_form', Feedback::SOURCES);
    }

    public function test_manual_entry_accepts_the_new_collection_sources(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        foreach (['social_media', 'sms'] as $source) {
            $response = $this->actingAs($admin)->post(route('feedback.manual.store'), [
                'patient_name' => 'Source Test',
                'email' => 'source-test@example.com',
                'phone' => '0712345678',
                'collection_means' => $source,
                'service_units' => [],
                'service_category' => 'opd',
                'feedback_type' => 'suggestion',
                'sentiment' => 'neutral',
                'theme' => 'client_experience',
                'wing' => 'private',
                'service_rating' => 'good',
                'confidentiality_respected' => '1',
                'visit_date' => '2026-09-17',
                'location' => 'hq',
                'overall_experience' => 'Collection source test feedback.',
                'improvement_suggestion' => '',
                'message' => 'Collection source test feedback.',
                'is_urgent' => '0',
                'consent_given' => '1',
            ]);

            $response->assertRedirect();
            $this->assertDatabaseHas('feedback_submissions', ['source' => $source]);
        }
    }

    public function test_legacy_paper_form_records_are_presented_as_manual_entry(): void
    {
        $feedback = new Feedback(['source' => 'paper_form']);

        $this->assertSame('Manual Entry', $feedback->getSourceLabel());
    }
}
