<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Modules\Identity\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Permissions
        $permissions = [
            'view-members' => 'Xem danh sách thành viên',
            'edit-members' => 'Sửa thông tin thành viên',
            'view-attendance' => 'Xem điểm danh',
            'check-in-attendance' => 'Thực hiện điểm danh',
            'view-finance' => 'Xem tài chính',
        ];

        foreach ($permissions as $slug => $name) {
            Permission::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        // 2. Create Roles
        $roles = [
            'super-admin' => 'Mục sư / Super Admin',
            'admin' => 'Thư ký / Admin',
            'leader' => 'Trưởng ban / Leader',
            'staff' => 'Nhân sự / Staff',
        ];

        foreach ($roles as $slug => $name) {
            Role::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        // 3. Assign Permissions to Roles (Basic setup)
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $superAdmin->permissions()->sync(Permission::all());

        $admin = Role::where('slug', 'admin')->first();
        $admin->permissions()->sync(Permission::whereIn('slug', ['view-members', 'edit-members', 'view-attendance', 'check-in-attendance'])->get());

        $leader = Role::where('slug', 'leader')->first();
        $leader->permissions()->sync(Permission::whereIn('slug', ['view-members', 'view-attendance', 'check-in-attendance'])->get());

        // 4. Assign Super Admin to first user
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->roles()->syncWithoutDetaching([$superAdmin->id]);
        }
    }
}
