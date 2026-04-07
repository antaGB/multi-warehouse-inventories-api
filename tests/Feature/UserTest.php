<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected User $actingUser;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'display_name' => "Super Admin"]);
        
        $this->actingUser  = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
    }

    // ==========================================
    // CREATE
    // ==========================================

    public function test_can_create_user(): void
    {
        Role::create(['name' => 'super-admin', 'display_name' => "Super Admin"]);

        $payload = [
            'name'     => 'Budi Santoso',
            'email'    => 'budi@example.com',
            'password' => 'password123',
            'role_id'  => 1,
        ];

        $response = $this->actingAs($this->actingUser, 'sanctum')
                        ->postJson('/api/users', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => ['id', 'name', 'email', 'role_id']
                 ]);

        $this->assertDatabaseHas('users', [
            'email'   => 'budi@example.com',
            'role_id' => 1,
        ]);
    }

    public function test_create_user_fails_with_missing_fields(): void
    {
        $response = $this->actingAs($this->actingUser, 'sanctum')
                    ->postJson('/api/users', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'password', 'role_id']);
    }

    public function test_create_user_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'budi@example.com']);

        $response = $this->actingAs($this->actingUser, 'sanctum')
                        ->postJson('/api/users', [
            'name'     => 'Budi Lain',
            'email'    => 'budi@example.com',
            'password' => 'password123',
            'role_id'  => 1,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_create_user_fails_with_invalid_email(): void
    {
        $response = $this->actingAs($this->actingUser, 'sanctum')
        ->postJson('/api/users', [
            'name'     => 'Budi Santoso',
            'email'    => 'not-an-email',
            'password' => 'password123',
            'role_id'  => 1,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    // ==========================================
    // READ
    // ==========================================

    public function test_can_get_all_users(): void
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->actingUser, 'sanctum')
                        ->getJson('/api/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'email', 'role_id']
                     ]
                 ]);
    }

    public function test_can_get_single_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->actingUser, 'sanctum')
                        ->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id'      => $user->id,
                         'name'    => $user->name,
                         'email'   => $user->email,
                         'role_id' => $user->role_id,
                     ]
                 ]);
    }

    public function test_get_user_returns_404_if_not_found(): void
    {
        $response = $this->actingAs($this->actingUser, 'sanctum')
                        ->getJson('/api/users/999');

        $response->assertStatus(404);
    }

    // ==========================================
    // UPDATE
    // ==========================================

    public function test_can_update_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->actingUser, 'sanctum')
                        ->putJson("/api/users/{$user->id}", [
            'name'    => 'Updated Name',
            'email'   => 'updated@example.com',
            'role_id' => $this->actingUser->role_id,
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'name'    => 'Updated Name',
                        'email'   => 'updated@example.com',
                        'role_id' => $this->actingUser->role_id,
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_update_user_returns_404_if_not_found(): void
    {
        $response = $this->actingAs($this->actingUser, 'sanctum')
                        ->putJson('/api/users/999', [
            'name'    => 'Updated Name',
            'email'   => 'updated@example.com',
            'role_id' => 1,
        ]);

        $response->assertStatus(404);
    }

    public function test_update_user_fails_with_duplicate_email(): void
    {
        $userA = User::factory()->create(['email' => 'a@example.com']);
        $userB = User::factory()->create(['email' => 'b@example.com']);

        $response = $this->actingAs($this->actingUser, 'sanctum')
                        ->putJson("/api/users/{$userB->id}", [
            'name'    => 'User B',
            'email'   => 'a@example.com', // already taken by userA
            'role_id' => 1,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    // ==========================================
    // DELETE
    // ==========================================

    public function test_can_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->actingUser, 'sanctum')
                        ->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200); // or 204 if no content

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_delete_user_returns_404_if_not_found(): void
    {
        $response = $this->actingAs($this->actingUser, 'sanctum')
                        ->deleteJson('/api/users/999');

        $response->assertStatus(404);
    }

}
