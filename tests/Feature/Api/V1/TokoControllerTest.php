<?php

namespace Tests\Feature\Api\V1;

use App\Models\MapUserToko;
use App\Models\MstToko;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokoControllerTest extends TestCase
{
    /** @test */
    public function test_list_tokos_as_superadmin()
    {
        $superadmin = $this->createUser(['role' => 'superadmin']);
        $this->createToko(['name' => 'Toko A']);
        $this->createToko(['name' => 'Toko B']);

        $response = $this->actingAsUser($superadmin)
            ->getJson('/api/v1/toko');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function test_list_tokos_as_admin()
    {
        $admin = $this->createUser(['role' => 'admin']);
        $this->createToko(['name' => 'Toko A']);

        $response = $this->actingAsUser($admin)
            ->getJson('/api/v1/toko');

        $response->assertStatus(200);
    }

    /** @test */
    public function test_list_tokos_as_kasir_denied()
    {
        $kasir = $this->createUser(['role' => 'kasir']);
        
        $response = $this->actingAsUser($kasir)
            ->getJson('/api/v1/toko');

        $response->assertStatus(403);
    }

    /** @test */
    public function test_create_toko_with_valid_data()
    {
        $superadmin = $this->createUser(['role' => 'superadmin']);

        $tokoData = [
            'name' => 'New Toko',
            'address' => 'Jl. Baru No. 1',
            'jenis_toko' => 'pusat',
            'admin_email' => 'admin.new@example.com',
            'kasir_email' => 'kasir.new@example.com'
        ];

        $response = $this->actingAsUser($superadmin)
            ->postJson('/api/v1/toko', $tokoData);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'New Toko',
                'jenis_toko' => 'pusat'
            ]);

        $this->assertDatabaseHas('mst_toko', ['name' => 'New Toko']);
    }

    /** @test */
    public function test_create_toko_auto_creates_admin_and_kasir()
    {
        $superadmin = $this->createUser(['role' => 'superadmin']);

        $tokoData = [
            'name' => 'Toko Auto',
            'address' => 'Jl. Auto',
            'jenis_toko' => 'cabang',
            'admin_email' => 'admin.auto@example.com',
            'kasir_email' => 'kasir.auto@example.com'
        ];

        $this->actingAsUser($superadmin)
            ->postJson('/api/v1/toko', $tokoData);

        $this->assertDatabaseHas('mst_user', ['email' => 'admin.auto@example.com', 'role' => 'admin']);
        $this->assertDatabaseHas('mst_user', ['email' => 'kasir.auto@example.com', 'role' => 'kasir']);
    }

    /** @test */
    public function test_create_toko_with_invalid_jenis_toko()
    {
        $superadmin = $this->createUser(['role' => 'superadmin']);

        $tokoData = [
            'name' => 'Toko Invalid',
            'address' => 'Jl. Invalid',
            'jenis_toko' => 'invalid_type', // Invalid
            'admin_email' => 'admin.inv@examples.com',
            'kasir_email' => 'kasir.inv@examples.com'
        ];

        $response = $this->actingAsUser($superadmin)
            ->postJson('/api/v1/toko', $tokoData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['jenis_toko']);
    }

    /** @test */
    public function test_get_toko_details()
    {
        $user = $this->createUser();
        $toko = $this->createToko();

        $response = $this->actingAsUser($user)
            ->getJson("/api/v1/toko/{$toko->toko_id}");

        $response->assertStatus(200)
            ->assertJson(['toko_id' => $toko->toko_id]);
    }

    /** @test */
    public function test_get_nonexistent_toko()
    {
        $user = $this->createUser();
        $fakeId = \Illuminate\Support\Str::uuid();

        $response = $this->actingAsUser($user)
            ->getJson("/api/v1/toko/{$fakeId}");

        $response->assertStatus(404);
    }

    /** @test */
    public function test_update_toko_as_superadmin()
    {
        $superadmin = $this->createUser(['role' => 'superadmin']);
        $toko = $this->createToko(['name' => 'Old Name']);

        $response = $this->actingAsUser($superadmin)
            ->putJson("/api/v1/toko/{$toko->toko_id}", [
                'name' => 'New Name'
            ]);

        $response->assertStatus(200)
            ->assertJson(['name' => 'New Name']);
    }

    /** @test */
    public function test_update_toko_without_permission()
    {
        $admin = $this->createUser(['role' => 'admin']);
        $toko = $this->createToko();

        $response = $this->actingAsUser($admin)
            ->putJson("/api/v1/toko/{$toko->toko_id}", ['name' => 'Hacked']);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_delete_toko_as_superadmin()
    {
        $superadmin = $this->createUser(['role' => 'superadmin']);
        $toko = $this->createToko();

        $response = $this->actingAsUser($superadmin)
            ->deleteJson("/api/v1/toko/{$toko->toko_id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('mst_toko', ['toko_id' => $toko->toko_id]);
    }

    /** @test */
    public function test_delete_toko_without_permission()
    {
        $admin = $this->createUser(['role' => 'admin']);
        $toko = $this->createToko();

        $response = $this->actingAsUser($admin)
            ->deleteJson("/api/v1/toko/{$toko->toko_id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function test_assign_user_to_toko()
    {
        $superadmin = $this->createUser(['role' => 'superadmin']);
        $user = $this->createUser();
        $toko = $this->createToko();

        $response = $this->actingAsUser($superadmin)
            ->postJson("/api/v1/toko/{$toko->toko_id}/assign", ['user_id' => $user->user_id]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('map_user_toko', [
            'user_id' => $user->user_id,
            'toko_id' => $toko->toko_id
        ]);
    }

    /** @test */
    public function test_remove_user_from_toko()
    {
        $superadmin = $this->createUser(['role' => 'superadmin']);
        $user = $this->createUser();
        $toko = $this->createToko();
        
        // Manual attach
        MapUserToko::create([
            'user_id' => $user->user_id,
            'toko_id' => $toko->toko_id
        ]);

        $response = $this->actingAsUser($superadmin)
            ->deleteJson("/api/v1/toko/{$toko->toko_id}/users/{$user->user_id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('map_user_toko', [
            'user_id' => $user->user_id,
            'toko_id' => $toko->toko_id
        ]);
    }

    /** @test */
    public function test_list_toko_users()
    {
        $admin = $this->createUser(['role' => 'admin']);
        $toko = $this->createToko();
        $user1 = $this->createUser();
        MapUserToko::create([
            'user_id' => $user1->user_id,
            'toko_id' => $toko->toko_id
        ]);

        $response = $this->actingAsUser($admin)
            ->getJson("/api/v1/toko/{$toko->toko_id}/users");

        $response->assertStatus(200)
            ->assertJsonFragment(['user_id' => $user1->user_id]);
    }

    /** @test */
    public function test_get_my_tokos()
    {
        $user = $this->createUser();
        $toko1 = $this->createToko(['name' => 'My Toko 1']);
        $toko2 = $this->createToko(['name' => 'Other Toko']);
        
        MapUserToko::create([
            'user_id' => $user->user_id,
            'toko_id' => $toko1->toko_id
        ]);

        $response = $this->actingAsUser($user)
            ->getJson('/api/v1/my-toko');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'My Toko 1'])
            ->assertJsonMissing(['name' => 'Other Toko']);
    }
}
