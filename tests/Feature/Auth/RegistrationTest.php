<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'fname' => 'Test',
            'mname' => 'Middle',
            'lname' => 'User',
            'dob' => '1995-05-20',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'is_first_user' => true,
        ]);
    }

    public function test_subsequent_users_are_redirected_to_registration_complete_and_marked_pending(): void
    {
        User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'is_first_user' => true,
        ]);

        $response = $this->post('/register', [
            'fname' => 'Pending',
            'lname' => 'Reviewer',
            'dob' => '1998-03-14',
            'email' => 'pending@example.com',
            'role' => User::ROLE_QA_OFFICER,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('register.complete'));

        $this->assertDatabaseHas('users', [
            'email' => 'pending@example.com',
            'role' => User::ROLE_QA_OFFICER,
            'is_active' => false,
            'is_first_user' => false,
        ]);
    }
}
