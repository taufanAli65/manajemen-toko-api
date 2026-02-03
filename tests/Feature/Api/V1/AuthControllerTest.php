<?php

namespace Tests\Feature\Api\V1;

use App\Models\MstUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /** @test */
    public function test_login_with_valid_credentials()
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    /** @test */
    public function test_login_with_invalid_credentials()
    {
        $this->createUser([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function test_login_with_missing_fields()
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /** @test */
    public function test_get_current_user_authenticated()
    {
        $user = $this->createUser(['role' => 'admin']);
        
        $response = $this->actingAsUser($user)
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'user_id' => $user->user_id,
                'email' => $user->email,
                'role' => 'admin',
            ]);
    }

    /** @test */
    public function test_get_current_user_unauthenticated()
    {
        $response = $this->getJson('/api/v1/auth/me');
        
        // Should be 401 because route is protected
        // Note: Check Exception Handler behavior if it redirects or returns json
        // usually api routes return JSON.
        $response->assertStatus(401); 
    }

    /** @test */
    public function test_logout_with_valid_token()
    {
        $user = $this->createUser();
        
        $response = $this->actingAsUser($user)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);
            
        // verifying token invalidation is tricky in integration tests without checks,
        // but status 200 implies controller action was hit.
    }

    /** @test */
    public function test_logout_without_token()
    {
        $response = $this->postJson('/api/v1/auth/logout');
        $response->assertStatus(401);
    }

    /** @test */
    public function test_register_user_as_superadmin()
    {
        $superadmin = $this->createUser(['role' => 'superadmin']);

        $userData = [
            'full_name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'password' => 'password123',
            'role' => 'admin'
        ];

        $response = $this->actingAsUser($superadmin)
            ->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'email' => 'newadmin@example.com',
                'role' => 'admin'
            ]);

        $this->assertDatabaseHas('mst_user', ['email' => 'newadmin@example.com']);
    }

    /** @test */
    public function test_register_user_as_admin_creates_kasir_only()
    {
        $admin = $this->createUser(['role' => 'admin']);

        // Admin tries to create another admin (should fail or force to kasir depending on logic)
        // Based on docs: "Admin (can only create kasir)"
        
        $userData = [
            'full_name' => 'New Kasir',
            'email' => 'newkasir@example.com',
            'password' => 'password123',
            // No role sent, should default/force to kasir or if sent admin it might be ignored/rejected
            // Let's assume controller logic: if admin, forced to kasir
        ];

        $response = $this->actingAsUser($admin)
            ->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('mst_user', [
            'email' => 'newkasir@example.com',
            'role' => 'kasir' // Assert it became kasir
        ]);
    }

    /** @test */
    public function test_register_with_duplicate_email()
    {
        $superadmin = $this->createUser(['role' => 'superadmin']);
        $existingUser = $this->createUser(['email' => 'existing@example.com']);

        $userData = [
            'full_name' => 'Duplicate User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'role' => 'kasir'
        ];

        $response = $this->actingAsUser($superadmin)
            ->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function test_register_without_permission()
    {
        $kasir = $this->createUser(['role' => 'kasir']);

        $userData = [
            'full_name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password123',
            'role' => 'superadmin'
        ];

        $response = $this->actingAsUser($kasir)
            ->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_update_own_profile()
    {
        $user = $this->createUser();
        
        $newData = [
            'full_name' => 'Updated Name',
            'password' => 'newpassword123'
        ];

        $response = $this->actingAsUser($user)
            ->putJson("/api/v1/auth/users/{$user->user_id}", $newData);

        $response->assertStatus(200)
            ->assertJson(['full_name' => 'Updated Name']);

        $this->assertDatabaseHas('mst_user', [
            'user_id' => $user->user_id,
            'full_name' => 'Updated Name'
        ]);
    }

    /** @test */
    public function test_update_other_user_as_superadmin()
    {
        $superadmin = $this->createUser(['role' => 'superadmin']);
        $targetUser = $this->createUser(['full_name' => 'Old Name']);

        $newData = ['full_name' => 'New Name by Superadmin'];

        $response = $this->actingAsUser($superadmin)
            ->putJson("/api/v1/auth/users/{$targetUser->user_id}", $newData);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('mst_user', [
            'user_id' => $targetUser->user_id,
            'full_name' => 'New Name by Superadmin'
        ]);
    }

    /** @test */
    public function test_update_other_user_without_permission()
    {
        $user1 = $this->createUser(['role' => 'kasir']);
        $user2 = $this->createUser(['role' => 'kasir']);

        $response = $this->actingAsUser($user1)
            ->putJson("/api/v1/auth/users/{$user2->user_id}", ['full_name' => 'Hacked']);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_list_users_as_superadmin()
    {
        $superadmin = $this->createUser(['role' => 'superadmin']);
        $this->createUser(['role' => 'admin']);
        $this->createUser(['role' => 'kasir']);

        $response = $this->actingAsUser($superadmin)
            ->getJson('/api/v1/auth/users');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
            
        // Assuming pagination defaults to 10 and we have 3 users
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function test_list_users_without_permission()
    {
        $admin = $this->createUser(['role' => 'admin']);
        
        $response = $this->actingAsUser($admin)
            ->getJson('/api/v1/auth/users');

        $response->assertStatus(403);
    }
}
