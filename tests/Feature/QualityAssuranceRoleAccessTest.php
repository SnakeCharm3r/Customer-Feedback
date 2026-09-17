<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QualityAssuranceRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_quality_assurance_officer_can_view_full_feedback_reports(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_QA_OFFICER]);

        $this->assertTrue($officer->canViewReports());
        $this->assertFalse($officer->canManageUsers());

        $report = $this->actingAs($officer)->get(route('reports.feedback.index'));
        $report->assertOk()
            ->assertSee('Feedback Reports')
            ->assertSee('Results')
            ->assertSee('Feedback Report')
            ->assertDontSee('Manage Users');

        $this->actingAs($officer)
            ->get(route('reports.analytics'))
            ->assertOk()
            ->assertSee('Analytics Dashboard');

        $this->actingAs($officer)
            ->get(route('reports.feedback.export.csv'))
            ->assertOk();
    }

    public function test_quality_assurance_officer_cannot_access_user_or_master_user_details(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_QA_OFFICER]);
        $regularUser = User::factory()->create(['role' => User::ROLE_CALL_CENTER]);
        $masterUser = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_first_user' => true,
        ]);

        $this->actingAs($officer)->get(route('users.index'))->assertForbidden();
        $this->actingAs($officer)->get(route('users.pending'))->assertForbidden();
        $this->actingAs($officer)->get(route('users.show', $regularUser))->assertForbidden();
        $this->actingAs($officer)->get(route('users.edit', $regularUser))->assertForbidden();
        $this->actingAs($officer)->get(route('users.show', $masterUser))->assertForbidden();
        $this->actingAs($officer)->get(route('users.edit', $masterUser))->assertForbidden();
        $this->actingAs($officer)->get(route('users.show', $officer))->assertForbidden();
        $this->actingAs($officer)->get(route('users.edit', $officer))->assertForbidden();

        // The normal personal profile remains available without exposing user management.
        $this->actingAs($officer)->get(route('profile.edit'))->assertOk();
    }

    public function test_quality_assurance_team_can_view_the_escalation_matrix(): void
    {
        foreach ([User::ROLE_QA_OFFICER, User::ROLE_QA_HOD] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->assertTrue($user->canViewEscalationMatrix());

            $this->actingAs($user)
                ->get(route('escalations.index'))
                ->assertOk()
                ->assertSee('Escalation Matrix')
                ->assertSee(route('escalations.index'), false);
        }
    }
}
