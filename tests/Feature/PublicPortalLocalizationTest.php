<?php

namespace Tests\Feature;

use App\Models\Feedback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPortalLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_portal_defaults_to_english(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Your Feedback Helps Us Serve You Better');
        $response->assertSee('Submit Feedback');
        $response->assertSee('Track Your Feedback');
    }

    public function test_user_can_switch_public_portal_to_kiswahili(): void
    {
        $this->from(route('home'))
            ->post(route('locale.switch'), [
                'locale' => 'sw',
            ])
            ->assertRedirect(route('home'))
            ->assertSessionHas('locale', 'sw');

        $response = $this->withSession(['locale' => 'sw'])->get(route('feedback.create'));

        $response->assertOk();
        $response->assertSee('Wasilisha Maoni Yako');
        $response->assertSee('Taarifa Muhimu');
        $response->assertSee('Fomu ya Maoni');
    }

    public function test_tracking_page_uses_selected_locale_for_feedback_labels(): void
    {
        $feedback = Feedback::create([
            'reference_no' => 'CCBRT-2026-00042',
            'patient_name' => 'Jane Patient',
            'email' => 'jane@example.com',
            'phone' => '0712345678',
            'service_units' => ['physician', 'laboratory'],
            'service_category' => 'outpatient',
            'feedback_type' => 'complaint',
            'service_rating' => 'poor',
            'confidentiality_respected' => true,
            'visit_date' => '2026-04-10',
            'overall_experience' => 'Huduma ilichelewa na mawasiliano hayakuwa wazi.',
            'message' => 'Ningependa kupata mrejesho wa haraka.',
            'is_urgent' => false,
            'consent_given' => true,
            'status' => 'under_review',
        ]);

        $response = $this->withSession(['locale' => 'sw'])->get(route('feedback.track', [
            'reference_no' => $feedback->reference_no,
        ]));

        $response->assertOk();
        $response->assertSee('Fuatilia Maoni Yako');
        $response->assertSee('Inachunguzwa');
        $response->assertSee('Huduma za Nje');
        $response->assertSee('Malalamiko');
    }
}
