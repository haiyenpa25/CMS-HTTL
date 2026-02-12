<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Identity\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Roles exist (just in case)
        $superAdminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Mục sư / Super Admin']);

        // 2. Create or Find Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                // 'member_id' => null // Optional if nullable
            ]
        );

        // 3. Assign Role
        if (!$user->roles->contains($superAdminRole->id)) {
            $user->roles()->attach($superAdminRole->id);
            $this->command->info('Assigned Super Admin role to admin@example.com');
        } else {
            $this->command->info('User admin@example.com already has Super Admin role');
        }
        
        $this->command->info('Admin User Setup Complete. Credentials: admin@example.com / password');
    }
}
