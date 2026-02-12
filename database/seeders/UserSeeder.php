<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Modules\Identity\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles
        $roles = [
            'super-admin' => 'Quản trị viên cấp cao',
            'admin' => 'Quản trị viên',
            'secretary' => 'Thư ký',
            'leader' => 'Trưởng ban',
            'staff' => 'Nhân sự',
            'user' => 'Thành viên',
        ];

        foreach ($roles as $slug => $name) {
            \App\Models\Role::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }
        
        // 2. Create Permissions (Basic)
        $permissions = [
            'view-members', 'edit-members', 
            'view-attendance', 'manage-attendance',
            'view-finance', 'manage-finance'
        ];
        foreach ($permissions as $slug) {
            \App\Models\Permission::firstOrCreate(['slug' => $slug], ['name' => ucfirst(str_replace('-', ' ', $slug))]);
        }

        // Assign Permissions to Roles (Basic mapping)
        $rolePermissions = [
            'super-admin' => $permissions,
            'admin' => $permissions,
            'secretary' => ['view-members', 'view-attendance', 'manage-attendance'],
            'leader' => ['view-members', 'view-attendance'], // Scoped by logic
            'staff' => ['view-members'],
        ];

        foreach ($rolePermissions as $roleSlug => $perms) {
            $role = \App\Models\Role::where('slug', $roleSlug)->first();
            $permIds = \App\Models\Permission::whereIn('slug', $perms)->pluck('id');
            $role->permissions()->syncWithoutDetaching($permIds);
        }

        // 3. Create Departments
        $deptThanhTrang = \App\Models\Department::firstOrCreate(
            ['name' => 'Ban Thanh Tráng'],
            ['type' => 'Sinh hoạt', 'status' => 'active', 'features' => ['attendance' => true]]
        );
        // Ensure attendance is enabled in SETTINGS too
        $deptThanhTrang->enableFeature('attendance');

        $deptThieuNhi = \App\Models\Department::firstOrCreate(
            ['name' => 'Ban Thiếu Nhi'],
            ['type' => 'Sinh hoạt', 'status' => 'active', 'features' => ['attendance' => true]]
        );
        $deptThieuNhi->enableFeature('attendance');
        
        // Create a SubGroup for Thieu Nhi
        $subGroupAuNhi = \App\Models\SubGroup::firstOrCreate(
            ['name' => 'Ấu Nhi', 'department_id' => $deptThieuNhi->id],
            // ['description' => 'Các bé 4-6 tuổi'] // Column does not exist
            []
        );

        // 4. Create Users
        
        // --- Thanh Trang (Leader) ---
        $uThanhTrang = User::firstOrCreate(
            ['email' => 'thanhtrang@tml.com'],
            ['name' => 'Trưởng Ban Thanh Tráng', 'password' => bcrypt('123456')]
        );
        $uThanhTrang->roles()->syncWithoutDetaching([\App\Models\Role::where('slug', 'leader')->first()->id]);
        // Assign to Dept Thanh Tráng (Full Access)
        \App\Modules\Identity\Models\UserAssignment::firstOrCreate([
            'user_id' => $uThanhTrang->id,
            'department_id' => $deptThanhTrang->id
        ], [
            'permissions' => ['attendance' => true, 'view-members' => true]
        ]);
        
        // Assign to Dept Thiếu Nhi (Restricted - e.g No Attendance Permission? Or Attendance only? User asked for "different permissions")
        // Let's give "View Members" ONLY in Thieu Nhi, NO Attendance.
        // Wait, User said: "với các quyền khác nhau để tôi test tính năng chuyển đổi ban"
        // Let's give Attendance in BOTH but maybe different subsets? 
        // Or simpler: give Attendance in BOTH so we can test Context Switching.
        \App\Modules\Identity\Models\UserAssignment::firstOrCreate([
            'user_id' => $uThanhTrang->id,
            'department_id' => $deptThieuNhi->id
        ], [
            'permissions' => ['attendance' => true] // Access attendance here too
        ]);
        
        // --- Thieu Nhi (Staff/SubGroup Leader) ---
        $uThieuNhi = User::firstOrCreate(
            ['email' => 'thieunhi@tml.com'],
            ['name' => 'Nhân sự Thiếu Nhi', 'password' => bcrypt('123456')]
        );
        $uThieuNhi->roles()->syncWithoutDetaching([\App\Models\Role::where('slug', 'staff')->first()->id]);
        // Assign to Dept AND SubGroup
        \App\Modules\Identity\Models\UserAssignment::firstOrCreate([
            'user_id' => $uThieuNhi->id,
            'department_id' => $deptThieuNhi->id,
            'sub_group_id' => $subGroupAuNhi->id
        ]);

        // --- Thu Ky (Secretary/Admin) ---
        $uThuKy = User::firstOrCreate(
            ['email' => 'thuky@tml.com'],
            ['name' => 'Thư Ký Hội Thánh', 'password' => bcrypt('123456')]
        );
        // User requested "Gán quyền Admin" but context is Secretary
        // Let's give 'admin' role as requested to ensure they see checks
        $uThuKy->roles()->syncWithoutDetaching([\App\Models\Role::where('slug', 'admin')->first()->id]);
        
        // 5. Create Scoped Sessions for Testing
        // Session For Thanh Trang Only
        \App\Models\AttendanceSession::firstOrCreate(
            ['name' => 'S1: Nhóm Thanh Tráng Tuần 1'],
            [
                'date' => now(), 
                'type' => 'department_meeting', 
                'status' => 'open', 
                'department_id' => $deptThanhTrang->id // SCOPED
            ]
        );

        // Session For Thieu Nhi Only
        \App\Models\AttendanceSession::firstOrCreate(
            ['name' => 'S2: Nhóm Thiếu Nhi Tuần 1'],
            [
                'date' => now(), 
                'type' => 'department_meeting', 
                'status' => 'open', 
                'department_id' => $deptThieuNhi->id // SCOPED
            ]
        );
        
        // Global Session (Sunday Service)
        \App\Models\AttendanceSession::firstOrCreate(
            ['name' => 'S3: Thờ Phượng Chúa Nhật (Global)'],
            [
                'date' => now(), 
                'type' => 'sunday_service', 
                'status' => 'open', 
                'department_id' => null // GLOBAL
            ]
        );
    }
}
