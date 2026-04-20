<?php

namespace Tests\Feature\Api;

use App\Models\Appointment;
use App\Models\Course;
use App\Models\User;
use Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApiFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'api');
        Role::findOrCreate('user', 'api');
    }

    public function test_jwt_login_returns_token(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);
        $user->assignRole('user');

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()->assertJsonStructure([
            'token',
            'token_type',
            'expires_in',
            'user',
        ]);
    }

    public function test_only_admin_can_create_courses(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $user->assignRole('user');

        $admin = User::factory()->create(['password' => 'password']);
        $admin->assignRole('admin');

        $userToken = auth('api')->attempt([
            'email' => $user->email,
            'password' => 'password',
        ]);

        $adminToken = auth('api')->attempt([
            'email' => $admin->email,
            'password' => 'password',
        ]);


        $payload = [
            'title' => 'Intro to APIs',
            'description' => 'Course description',
            'duration_minutes' => 90,
        ];

        $this->withHeader('Authorization', "Bearer {$userToken}")
            ->postJson('/api/courses', $payload)
            ->assertForbidden();

        $this->withHeader('Authorization', "Bearer {$adminToken}")
            ->postJson('/api/courses', $payload)
            ->assertCreated();
    }

    public function test_enrollment_must_be_unique_per_user_and_course(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $course = Course::create([
            'title' => 'Laravel Fundamentals',
            'description' => 'Basics',
            'duration_minutes' => 120,
        ]);

        $token = auth('api')->login($user);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/enroll', ['course_id' => $course->id])
            ->assertCreated();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/enroll', ['course_id' => $course->id])
            ->assertUnprocessable();
    }

    public function test_appointments_are_user_scoped(): void
    {
        $userA = User::factory()->create();
        $userA->assignRole('user');

        $userB = User::factory()->create();
        $userB->assignRole('user');

        Appointment::create([
            'user_id' => $userA->id,
            'practitioner_name' => 'Dr. A',
            'appointment_at' => now()->addDay(),
            'status' => 'booked',
            'notes' => null,
        ]);

        Appointment::create([
            'user_id' => $userB->id,
            'practitioner_name' => 'Dr. B',
            'appointment_at' => now()->addDays(2),
            'status' => 'booked',
            'notes' => null,
        ]);

        $token = auth('api')->login($userA);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/appointments');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.practitioner_name', 'Dr. A');
    }
}
