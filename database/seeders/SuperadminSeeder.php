<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\MstUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MstUser::create([
            'user_id' => Str::uuid(),
            'role' => UserRole::SUPERADMIN,
            'email' => 'superadmin@manajemen-toko.com',
            'password' => Hash::make('password'),
            'full_name' => 'Super Admin',
            'is_deleted' => false,
            'created_by' => 'system',
        ]);

        $this->command->info('Superadmin user created successfully!');
        $this->command->info('Email: superadmin@manajemen-toko.com');
        $this->command->info('Password: password');
    }
}
