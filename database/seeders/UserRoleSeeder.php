<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        \App\Models\Role::create(['role_name' => 'Admin']); // role_id 1
        \App\Models\Role::create(['role_name' => 'Faculty']); // role_id 2

        // Create Statuses
        \App\Models\Status::create(['status_name' => 'Borrowed']);
        \App\Models\Status::create(['status_name' => 'Returned']);

        // Create the "Hardcoded" Admin
        \App\Models\User::create([
            'name' => 'PE Admin',
            'email' => 'admin@um.edu.ph',
            'password' => bcrypt('admin123'),
            'role_id' => 1, // Admin role
        ]);
    }
}
