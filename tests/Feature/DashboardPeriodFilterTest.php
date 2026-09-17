<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPeriodFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_dashboard_defaults_to_the_current_week(): void
    {
        Carbon::setTestNow('2026-09-17 12:00:00');
        $user = User::factory()->create(['role' => User::ROLE_QA_OFFICER]);

        $this->feedback('TODAY-FEEDBACK', '2026-09-17 09:00:00');
        $this->feedback('WEEK-FEEDBACK', '2026-09-15 09:00:00');
        $this->feedback('QUARTER-FEEDBACK', '2026-07-10 09:00:00');
        $this->feedback('OLD-FEEDBACK', '2026-04-10 09:00:00');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('period', 'week')
            ->assertViewHas('totalFeedback', 2)
            ->assertSee('This Week')
            ->assertSee('TODAY-FEEDBACK')
            ->assertSee('WEEK-FEEDBACK')
            ->assertDontSee('QUARTER-FEEDBACK')
            ->assertDontSee('OLD-FEEDBACK');
    }

    public function test_dashboard_period_filter_supports_today_quarter_and_all_time(): void
    {
        Carbon::setTestNow('2026-09-17 12:00:00');
        $user = User::factory()->create(['role' => User::ROLE_QA_OFFICER]);

        $this->feedback('TODAY-FEEDBACK', '2026-09-17 09:00:00');
        $this->feedback('WEEK-FEEDBACK', '2026-09-15 09:00:00');
        $this->feedback('QUARTER-FEEDBACK', '2026-07-10 09:00:00');
        $this->feedback('OLD-FEEDBACK', '2026-04-10 09:00:00');

        $this->actingAs($user)
            ->get(route('dashboard', ['period' => 'today']))
            ->assertOk()
            ->assertViewHas('totalFeedback', 1)
            ->assertSee('TODAY-FEEDBACK')
            ->assertDontSee('WEEK-FEEDBACK');

        $this->actingAs($user)
            ->get(route('dashboard', ['period' => 'quarter']))
            ->assertOk()
            ->assertViewHas('totalFeedback', 3)
            ->assertSee('QUARTER-FEEDBACK')
            ->assertDontSee('OLD-FEEDBACK');

        $this->actingAs($user)
            ->get(route('dashboard', ['period' => 'all']))
            ->assertOk()
            ->assertViewHas('totalFeedback', 4)
            ->assertSee('OLD-FEEDBACK');
    }

    private function feedback(string $reference, string $createdAt): Feedback
    {
        $feedback = Feedback::create([
            'reference_no' => $reference,
            'patient_name' => $reference,
            'service_category' => 'opd',
            'feedback_type' => 'complaint',
            'message' => $reference,
            'source' => 'portal',
            'location' => 'hq',
            'status' => 'new',
            'consent_given' => true,
        ]);

        $feedback->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->save();

        return $feedback;
    }
}
