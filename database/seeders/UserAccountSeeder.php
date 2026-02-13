<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Modules\Identity\Models\Role;
use App\Modules\Identity\Models\Permission;
use App\Modules\Identity\Models\User;
use App\Modules\Organization\Models\Department;
use App\Modules\Identity\Models\UserAssignment;

class UserAccountSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $this->command->info('Start seeding User Accounts (Roles, Permissions, Users)...');
            $password = Hash::make('TML@2025');

            // 1. Create Roles
            $roles = [
                'super-admin' => 'Quản trị viên cấp cao',
                'admin' => 'Quản trị viên',
                'leader' => 'Trưởng ban',
                'deputy' => 'Phó ban',
                'secretary' => 'Thư ký',
                'treasurer' => 'Thủ quỹ',
                'commissioner' => 'Ủy viên',
                'group_leader' => 'Tổ trưởng',
                'member' => 'Thành viên',
            ];

            foreach ($roles as $slug => $name) {
             // 1. Check by Slug
             $role = Role::where('slug', $slug)->first();
             if ($role) {
                 // Found by slug. usage of name is for display, so update it if different
                 if ($role->name !== $name) {
                     // Check if name is taken by another role
                     $nameTaken = Role::where('name', $name)->where('id', '!=', $role->id)->exists();
                     if (!$nameTaken) {
                         $role->update(['name' => $name]);
                     } else {
                         $this->command->warn("Role slug '$slug' exists, but wanted name '$name' is taken by another role. Keeping existing name '{$role->name}'.");
                     }
                 }
             } else {
                 // Slug not found. Check by Name.
                 $role = Role::where('name', $name)->first();
                 if ($role) {
                     // Found by name. Update slug to match our convention.
                     // We know slug is available because step 1 didn't find it.
                     $this->command->info("Updating Role '{$name}' slug from '{$role->slug}' to '$slug'");
                     $role->update(['slug' => $slug]);
                 } else {
                     // Neither found. Create new.
                     Role::create(['slug' => $slug, 'name' => $name]);
                 }
             }
        }

            // 2. Create Permissions
            $permissions = [
                'view-dashboard',
                'manage-users',
                'manage-members',
                'manage-attendance',
                'manage-departments',
                'manage-assets',
                'view-reports',
            ];

            foreach ($permissions as $slug) {
                Permission::firstOrCreate(['slug' => $slug], ['name' => ucfirst(str_replace('-', ' ', $slug))]);
            }

            // 3. Assign Permissions to Roles (Simplified)
            $rolePermissions = [
                'super-admin' => $permissions,
                'admin' => $permissions,
                'leader' => ['view-dashboard', 'manage-members', 'manage-attendance', 'view-reports'], 
                'deputy' => ['view-dashboard', 'manage-members', 'manage-attendance', 'view-reports'],
                'secretary' => ['view-dashboard', 'manage-members', 'manage-attendance', 'view-reports'],
                'treasurer' => ['view-dashboard', 'manage-assets'], // Assuming asset management
                'commissioner' => ['view-dashboard', 'manage-attendance'],
                'group_leader' => ['view-dashboard', 'manage-attendance'],
                'member' => [],
            ];

            foreach ($rolePermissions as $roleSlug => $perms) {
                $role = Role::where('slug', $roleSlug)->first();
                if ($role) {
                    $permIds = Permission::whereIn('slug', $perms)->pluck('id');
                    $role->permissions()->sync($permIds);
                }
            }

            // 4. Create Admin Users
            $admins = [
                'superadmin' => ['name' => 'Super Admin', 'role' => 'super-admin'],
                'admin' => ['name' => 'Admin', 'role' => 'admin']
            ];

            foreach ($admins as $username => $data) {
                $user = User::firstOrCreate(
                    ['email' => $username . '@httlthanhmyloi.com'], 
                    [
                        // Ensure password and name are set if creating new
                        'name' => $data['name'], 
                        'password' => $password
                    ]
                );
                // Force update password if user exists but has old password? No, firstOrCreate won't update.
                // But we want to ensure Roles are synced.
                $user->roles()->sync(Role::where('slug', $data['role'])->pluck('id'));
            }

            // 5. Create Department Users (Ban Sinh Hoạt)
            // Get all "Sinh hoạt" departments
            $activityDepts = Department::where('type', 'Sinh hoạt')->get();
            
            if ($activityDepts->isEmpty()) {
                 $this->command->warn('No "Sinh hoạt" departments found! Make sure MasterDataSeeder ran correctly.');
            }

            $positions = [
                'tb' => ['role' => 'leader', 'title' => 'Trưởng ban'],
                'pb' => ['role' => 'deputy', 'title' => 'Phó ban'],
                'tk' => ['role' => 'secretary', 'title' => 'Thư ký'],
                'uv' => ['role' => 'commissioner', 'title' => 'Ủy viên'],
                'tq' => ['role' => 'treasurer', 'title' => 'Thủ quỹ'],
                'tt' => ['role' => 'group_leader', 'title' => 'Tổ trưởng'],
            ];

            foreach ($activityDepts as $dept) {
                $deptSlug = \Illuminate\Support\Str::slug($dept->name);
                $deptSlug = str_replace(['ban-', '-'], '', $deptSlug); // Remove prefix 'ban-' and hyphens
                foreach ($positions as $prefix => $pos) {
                    $username = "{$prefix}.{$deptSlug}";
                    $email = "{$username}@httlthanhmyloi.com";
                    $fullName = "{$pos['title']} {$dept->name}";
                    
                    try {
                        $user = User::firstOrCreate(
                            ['email' => $email],
                            [
                                'name' => $fullName,
                                'password' => $password
                            ]
                        );

                        // Assign Role
                        $role = Role::where('slug', $pos['role'])->first();
                        if ($role) {
                            $user->roles()->sync([$role->id]);
                        }

                        // Assign to Department
                        UserAssignment::firstOrCreate(
                            ['user_id' => $user->id, 'department_id' => $dept->id],
                            ['permissions' => ['attendance' => true, 'view-members' => true]]
                        );
                    } catch (\Exception $e) {
                         $this->command->error("Failed for $email: " . $e->getMessage());
                         throw $e; // Re-throw to stop
                    }
                }
            }
            
            $this->command->info('Created Admin/Superadmin (Pass: TML@2025)');
            $this->command->info('Created Department Users for "Sinh hoạt" block (Format: tb.slug@tml.com, Pass: TML@2025)');

        } catch (\Exception $e) {
            $this->command->error('Error in UserAccountSeeder: ' . $e->getMessage());
            // $this->command->error($e->getTraceAsString());
        }
    }
}
