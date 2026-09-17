<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Feedback;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackReportOperationalFiltersTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_report_filters_and_breakdowns_share_the_same_scope(): void
    {
        Carbon::setTestNow('2026-09-17 12:00:00');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $owner = User::factory()->create(['role' => User::ROLE_QA_OFFICER]);
        $selectedDepartment = Department::create(['name' => 'Eye Department', 'is_active' => true]);
        $otherDepartment = Department::create(['name' => 'Other Department', 'is_active' => true]);

        $matching = $this->feedback('MATCHING-FEEDBACK', $selectedDepartment, $owner, [
            'location' => 'hq',
            'wing' => 'private',
            'theme' => 'waiting_time',
            'status' => 'under_review',
            'created_at' => '2026-09-10 08:00:00',
            'reviewed_at' => '2026-09-11 12:00:00',
        ]);

        $this->feedback('OTHER-FEEDBACK', $otherDepartment, null, [
            'location' => 'moshi',
            'wing' => 'standard',
            'theme' => 'client_experience',
            'status' => 'new',
            'created_at' => '2026-09-10 09:00:00',
        ]);

        $filters = [
            'date_from' => '2026-09-10',
            'date_to' => '2026-09-10',
            'department_id' => $selectedDepartment->id,
            'location' => 'hq',
            'wing' => 'private',
            'theme' => 'waiting_time',
            'status' => 'under_review',
            'assigned_to' => $owner->id,
            'delay' => 'delayed',
        ];

        $response = $this->actingAs($admin)->get(route('reports.feedback.index', $filters));

        $response->assertOk()
            ->assertSee($matching->reference_no)
            ->assertDontSee('OTHER-FEEDBACK')
            ->assertSee('Private Wing')
            ->assertSee('Waiting Time')
            ->assertSee('CCBRT Hospital HQ')
            ->assertSee('Delayed 4h')
            ->assertSee('Owner: ' . $owner->getFullName());
    }

    public function test_open_status_only_returns_new_feedback_older_than_twenty_four_hours(): void
    {
        Carbon::setTestNow('2026-09-17 12:00:00');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $department = Department::create(['name' => 'General Department', 'is_active' => true]);

        $this->feedback('AGED-OPEN-FEEDBACK', $department, null, [
            'status' => 'new',
            'created_at' => '2026-09-15 11:00:00',
        ]);
        $this->feedback('FRESH-NEW-FEEDBACK', $department, null, [
            'status' => 'new',
            'created_at' => '2026-09-17 08:00:00',
        ]);

        $response = $this->actingAs($admin)->get(route('reports.feedback.index', ['status' => 'open']));

        $response->assertOk();

        $reports = $response->viewData('reports');
        $this->assertSame(1, $reports->total());
        $this->assertSame('AGED-OPEN-FEEDBACK', $reports->first()->reference_no);
    }

    public function test_csv_export_uses_the_operational_filters_and_includes_accountability_columns(): void
    {
        Carbon::setTestNow('2026-09-17 12:00:00');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $owner = User::factory()->create(['role' => User::ROLE_QA_OFFICER]);
        $department = Department::create(['name' => 'Maternity', 'is_active' => true]);

        $this->feedback('CSV-MATCH', $department, $owner, [
            'theme' => 'waiting_time',
            'created_at' => '2026-09-12 08:00:00',
        ]);
        $this->feedback('CSV-OTHER', $department, null, [
            'theme' => 'client_experience',
            'created_at' => '2026-09-12 09:00:00',
        ]);

        $response = $this->actingAs($admin)->get(route('reports.feedback.export.csv', [
            'theme' => 'waiting_time',
            'delay' => 'delayed',
        ]));

        $response->assertOk();
        $csv = $response->streamedContent();

        $this->assertStringContainsString('Review Timing', $csv);
        $this->assertStringContainsString('Delay Owner', $csv);
        $this->assertStringContainsString('CSV-MATCH', $csv);
        $this->assertStringContainsString($owner->getFullName(), $csv);
        $this->assertStringNotContainsString('CSV-OTHER', $csv);
    }

    public function test_printable_report_uses_and_displays_all_operational_filters(): void
    {
        Carbon::setTestNow('2026-09-17 12:00:00');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $owner = User::factory()->create(['role' => User::ROLE_QA_OFFICER]);
        $selectedDepartment = Department::create(['name' => 'Selected Print Department', 'is_active' => true]);
        $otherDepartment = Department::create(['name' => 'Excluded Print Department', 'is_active' => true]);

        $this->feedback('PRINT-MATCH', $selectedDepartment, $owner, [
            'location' => 'hq',
            'wing' => 'private',
            'theme' => 'waiting_time',
            'status' => 'under_review',
            'reviewed_by' => $owner->id,
            'created_at' => '2026-09-10 08:00:00',
            'reviewed_at' => '2026-09-11 12:00:00',
        ]);
        $this->feedback('PRINT-EXCLUDED', $otherDepartment, null, [
            'location' => 'moshi',
            'wing' => 'standard',
            'theme' => 'client_experience',
            'created_at' => '2026-09-10 09:00:00',
        ]);

        $response = $this->actingAs($admin)->get(route('reports.feedback.export.pdf', [
            'date_from' => '2026-09-10',
            'date_to' => '2026-09-10',
            'department_id' => $selectedDepartment->id,
            'location' => 'hq',
            'wing' => 'private',
            'theme' => 'waiting_time',
            'status' => 'under_review',
            'assigned_to' => $owner->id,
            'reviewed_by' => $owner->id,
            'delay' => 'delayed',
        ]));

        $response->assertOk()
            ->assertSee('PRINT-MATCH')
            ->assertDontSee('PRINT-EXCLUDED')
            ->assertSee('Dates: 10 Sep 2026 to 10 Sep 2026')
            ->assertSee('Department: Selected Print Department')
            ->assertSee('Location: CCBRT Hospital HQ')
            ->assertSee('Ward / Wing: Private Wing')
            ->assertSee('Theme: Waiting Time')
            ->assertSee('Status: Under Review')
            ->assertSee('Review Timing: Delayed / Overdue')
            ->assertSee('Responsible: ' . $owner->getFullName())
            ->assertSee('Reviewer: ' . $owner->getFullName())
            ->assertSee('Delayed 4h');
    }

    private function feedback(string $reference, Department $department, ?User $owner, array $overrides = []): Feedback
    {
        $createdAt = $overrides['created_at'] ?? now();

        $feedback = Feedback::create(array_merge([
            'reference_no' => $reference,
            'patient_name' => $reference,
            'service_category' => 'opd',
            'feedback_type' => 'complaint',
            'message' => $reference,
            'source' => 'portal',
            'location' => 'hq',
            'wing' => 'private',
            'theme' => 'waiting_time',
            'sentiment' => 'negative',
            'status' => 'new',
            'department_id' => $department->id,
            'assigned_to' => $owner?->id,
            'consent_given' => true,
        ], $overrides));

        $feedback->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->save();

        return $feedback->fresh(['assignedTo', 'reviewedBy', 'department']);
    }
}
