<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_pending_users_page(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'is_first_user' => true,
        ]);

        $pendingUser = User::factory()->create([
            'fname' => 'Pending',
            'lname' => 'User',
            'email' => 'pending-user@example.com',
            'role' => User::ROLE_CALL_CENTER,
            'is_active' => false,
            'is_first_user' => false,
            'approved_at' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('users.pending'));

        $response->assertOk();
        $response->assertSee('Users Awaiting Approval');
        $response->assertSee($pendingUser->email);
    }

    public function test_admin_can_approve_a_pending_user(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'is_first_user' => true,
        ]);

        $pendingUser = User::factory()->create([
            'role' => User::ROLE_CALL_CENTER,
            'is_active' => false,
            'is_first_user' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        $response = $this->actingAs($admin)->post(route('users.approve', $pendingUser), [
            'role' => User::ROLE_QA_OFFICER,
        ]);

        $response->assertRedirect(route('users.pending'));

        $this->assertDatabaseHas('users', [
            'id' => $pendingUser->id,
            'role' => User::ROLE_QA_OFFICER,
            'is_active' => true,
            'approved_by' => $admin->id,
        ]);
    }
}
