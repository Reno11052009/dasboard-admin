<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
public function run(): void
    {
        $superAdminRole = \App\Models\Role::where('name', 'Super Admin')->first();
        $adminRole = \App\Models\Role::where('name', 'Admin')->first();
        $userRole = \App\Models\Role::where('name', 'User')->first();

        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('12345678'),
            'role_id' => $superAdminRole->id ?? null,
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678'),
            'role_id' => $adminRole->id ?? null,
        ]);

        User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('12345678'),
            'role_id' => $userRole->id ?? null,
        ]);

        User::factory(7)->create();
    }
}
