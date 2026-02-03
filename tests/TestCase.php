<?php

namespace Tests;

use App\Models\MstUser;
use App\Models\MstToko;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function createUser(array $attributes = [], string $role = 'kasir')
    {
        return MstUser::create(array_merge([
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'full_name' => fake()->name(),
            'role' => $role,
            'created_by' => 'system'
        ], $attributes));
    }

    protected function createToko(array $attributes = [])
    {
        return MstToko::create(array_merge([
            'name' => fake()->company(),
            'address' => fake()->address(),
            'jenis_toko' => 'retail',
            'created_by' => 'system'
        ], $attributes));
    }

    protected function actingAsUser(MstUser $user)
    {
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);
        return $this->actingAs($user);
    }
}
