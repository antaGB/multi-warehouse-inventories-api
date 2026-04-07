<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $adminRole = Role::create(['name' => 'super-admin', 'display_name' => "Super Admin"]);


        User::create([
            'name' => 'Super Admin',
            'role_id' => $adminRole->id,
            'email' => 'admin@test.com',
            'password' => Hash::make('pass123')
        ]);
    }
}
