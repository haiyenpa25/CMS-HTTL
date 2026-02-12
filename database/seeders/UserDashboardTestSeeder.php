<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Member;
use App\Models\Role;
use App\Modules\Identity\Models\User;
use App\Modules\Identity\Models\UserAssignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserDashboardTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // 1. Tạo các Department (Ban ngành)
            $departments = $this->createDepartments();
            
            // 2. Tạo Member cho test user
            $member = $this->createTestMember();
            
            // 3. Tạo test user và gán quyền
            $this->createTestUser($member, $departments);

            DB::commit();
            
            $this->command->info('✅ Đã tạo thành công dữ liệu test cho User Dashboard!');
            $this->command->info("   - {$departments->count()} departments");
            $this->command->info("   - Test user: thanhtrang@tml.com (password: 123456)");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Lỗi: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createDepartments()
    {
        $this->command->info('Đang tạo các Department (Ban ngành)...');
        
        $departmentsData = [
            // Ban Lãnh đạo
            [
                'name' => 'Ban Chấp Hành',
                'type' => 'Lãnh đạo',
                'description' => 'Ban lãnh đạo chung của hội thánh',
                'features' => ['attendance' => true, 'scheduling' => true, 'inventory' => true, 'visits' => true],
                'status' => 'active'
            ],
            [
                'name' => 'Ban Truyền Giáo',
                'type' => 'Lãnh đạo',
                'description' => 'Phụ trách công tác truyền giáo',
                'features' => ['attendance' => true, 'scheduling' => true, 'visits' => true],
                'status' => 'active'
            ],
            
            // Ban Sinh hoạt
            [
                'name' => 'Ban Thanh Tráng',
                'type' => 'Sinh hoạt',
                'description' => 'Ban sinh hoạt thanh niên',
                'features' => ['attendance' => true, 'scheduling' => true, 'report_entry' => true, 'visits' => true],
                'status' => 'active'
            ],
            [
                'name' => 'Ban Thiếu Nhi',
                'type' => 'Sinh hoạt',
                'description' => 'Ban sinh hoạt thiếu nhi',
                'features' => ['attendance' => true],
                'status' => 'active'
            ],
            [
                'name' => 'Ban Gia Đình',
                'type' => 'Sinh hoạt',
                'description' => 'Ban sinh hoạt gia đình',
                'features' => ['attendance' => true, 'scheduling' => true, 'visits' => true],
                'status' => 'active'
            ],
            
            // Ban Mục vụ
            [
                'name' => 'Ban Thờ Phượng',
                'type' => 'Mục vụ',
                'description' => 'Phụ trách công tác thờ phượng',
                'features' => ['attendance' => true, 'scheduling' => true, 'inventory' => true],
                'status' => 'active'
            ],
            [
                'name' => 'Ban Âm Nhạc',
                'type' => 'Mục vụ',
                'description' => 'Phụ trách công tác âm nhạc',
                'features' => ['attendance' => true, 'scheduling' => true],
                'status' => 'active'
            ],
            [
                'name' => 'Ban Tiếp Tân',
                'type' => 'Mục vụ',
                'description' => 'Phụ trách công tác tiếp tân',
                'features' => ['attendance' => true],
                'status' => 'active'
            ],
            [
                'name' => 'Ban Đoàn Khế',
                'type' => 'Mục vụ',
                'description' => 'Phụ trách công tác đoàn khế',
                'features' => ['attendance' => true, 'scheduling' => true, 'visits' => true],
                'status' => 'active'
            ],
        ];

        $departments = collect();
        foreach ($departmentsData as $data) {
            $dept = Department::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
            $departments->push($dept);
        }

        return $departments;
    }

    private function createTestMember()
    {
        $this->command->info('Đang tạo Member cho test user...');
        
        // Lấy family đầu tiên hoặc tạo mới
        $family = \App\Models\Family::first();
        if (!$family) {
            $family = \App\Models\Family::create([
                'name' => 'Gia đình Thanh Trang',
                'address' => '123 Test Street',
                'ward' => 'Phường 1',
                'district' => 'Quận 1',
                'province' => 'TP.HCM'
            ]);
        }

        // Lấy title đầu tiên
        $title = \App\Models\Title::first();
        if (!$title) {
            $title = \App\Models\Title::create([
                'name' => 'Tín hữu',
                'slug' => 'tin-huu',
                'level' => 1
            ]);
        }

        $member = Member::firstOrCreate(
            ['email' => 'thanhtrang@tml.com'],
            [
                'family_id' => $family->id,
                'title_id' => $title->id,
                'full_name' => 'Nguyễn Thanh Trang',
                'phone' => '0901234567',
                'email' => 'thanhtrang@tml.com',
                'gender' => 'Nữ',
                'birthday' => now()->subYears(25),
                'job' => ['name' => 'Nhân viên văn phòng', 'company' => 'Công ty ABC'],
                'is_married' => false,
                'date_faith' => now()->subYears(10),
                'date_baptism' => now()->subYears(8),
                'joined_date' => now()->subYears(5),
                'status' => 'active',
                'last_visited_at' => now(),
            ]
        );

        return $member;
    }

    private function createTestUser($member, $departments)
    {
        $this->command->info('Đang tạo test user thanhtrang@tml.com...');
        
        // Tạo hoặc lấy role 'leader'
        $leaderRole = Role::firstOrCreate(
            ['slug' => 'leader'],
            ['name' => 'Leader']
        );

        // Tạo user
        $user = User::firstOrCreate(
            ['email' => 'thanhtrang@tml.com'],
            [
                'name' => 'Thanh Trang',
                'email' => 'thanhtrang@tml.com',
                'password' => Hash::make('123456'),
                'member_id' => $member->id,
            ]
        );

        // Gán role
        if (!$user->roles()->where('role_id', $leaderRole->id)->exists()) {
            $user->roles()->attach($leaderRole->id);
        }

        // Tìm "Ban Thanh Tráng"
        $thanhTrangDept = $departments->firstWhere('name', 'Ban Thanh Tráng');
        
        if ($thanhTrangDept) {
            // Tạo user assignment với permissions cụ thể
            UserAssignment::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'department_id' => $thanhTrangDept->id,
                ],
                [
                    'permissions' => [
                        'attendance' => true,        // Điểm Danh Chủ Nhật
                        'report_entry' => true,      // Nhập Báo Cáo
                        'scheduling' => false,       // Không có quyền Phân Công
                        'visits' => true,            // Có quyền Thăm Viếng
                        'reports' => false,          // Không có quyền Báo Cáo
                    ]
                ]
            );
            
            $this->command->info("   ✓ Đã gán user vào '{$thanhTrangDept->name}' với quyền: attendance, report_entry, visits");
        }
    }
}
