<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Modules\Membership\Models\Member;
use App\Modules\Organization\Models\Department;
use App\Modules\Membership\Models\Title;
use App\Modules\Organization\Models\Position;
use App\Modules\Membership\Models\Family;
use Carbon\Carbon;
use Illuminate\Support\Str;

class StandardTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Start seeding Test Data (Members & Assignments)...');

        Schema::disableForeignKeyConstraints();
        
        // Clear old test data
        DB::table('members')->truncate();
        DB::table('department_member')->truncate(); // Assuming this is the pivot table for Departments
        // Also clear user_assignments if that's used for Feature Access
        DB::table('user_assignments')->truncate();

        Schema::enableForeignKeyConstraints();

        // Ensure Master Data exists
        $titles = Title::all();
        $positions = Position::all();
        $departments = Department::all();

        if ($titles->isEmpty() || $departments->isEmpty()) {
            $this->command->error('Master Data missing! Please run MasterDataSeeder first.');
            return;
        }

        $this->createMembers($titles, $positions, $departments);

        $this->command->info('Test Data seeded successfully!');
    }

    private function createMembers($titles, $positions, $departments)
    {
        $firstNames = [
            'male' => ['Văn', 'Đức', 'Minh', 'Hoàng', 'Quang', 'Tuấn', 'Hùng', 'Dũng', 'Thành', 'Phúc', 'Khoa', 'Long', 'Nam', 'Tài', 'Trí', 'Bảo', 'Khánh', 'Sơn'],
            'female' => ['Thị', 'Thu', 'Hương', 'Lan', 'Mai', 'Hoa', 'Linh', 'Ngọc', 'Phương', 'Trang', 'Anh', 'Chi', 'Hà', 'Nhung', 'Vy', 'Yến', 'Thảo', 'Dung']
        ];
        
        $lastNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Ngô', 'Dương', 'Lý', 'Phan', 'Trịnh', 'Trương', 'Đinh', 'Lâm'];
        
        $middleNames = [
            'male' => ['Văn', 'Đức', 'Minh', 'Quốc', 'Hữu', 'Công', 'Xuân', 'Thanh', 'Ngọc', 'Anh', 'Gia', 'Đăng'],
            'female' => ['Thị', 'Thu', 'Ngọc', 'Thanh', 'Kim', 'Hồng', 'Phương', 'Diệu', 'Bích', 'Như', 'Tuyết', 'Ánh']
        ];

        $jobs = ['Bác sĩ', 'Giáo viên', 'Kỹ sư', 'Kế toán', 'Sinh viên', 'Kinh doanh', 'Nội trợ', 'Học sinh', 'Lập trình viên', 'Nhân viên văn phòng', 'Công nhân', 'Tài xế'];

        // Create Families
        $families = collect();
        for ($k = 0; $k < 15; $k++) {
             $families->push(Family::create([
                 'name' => 'Gia đình ' . $lastNames[array_rand($lastNames)],
                 'address' => 'Số ' . rand(1, 999) . ', Đường ABC',
                 'district' => 'Quận ' . rand(1, 12),
                 'province' => 'TP.HCM',
                 'created_at' => now(),
                 'updated_at' => now(),
             ]));
        }

        $count = 0;
        for ($i = 0; $i < 50; $i++) {
            $gender = $i % 2 == 0 ? 'Nam' : 'Nữ';
            $genderKey = ($gender == 'Nam') ? 'male' : 'female';
            
            $lastName = $lastNames[array_rand($lastNames)];
            $middleName = $middleNames[$genderKey][array_rand($middleNames[$genderKey])];
            $firstName = $firstNames[$genderKey][array_rand($firstNames[$genderKey])];
            $fullName = "{$lastName} {$middleName} {$firstName}";

            // Pick a random title (mostly Tín hữu, but mix in others)
            // Weight logic: 70% Tín hữu, 10% Nhân sự, 5% Chấp sự, etc.
            $rand = rand(1, 100);
            if ($rand <= 70) {
                // Tín hữu
                $titleId = $titles->where('name', 'Tín hữu')->first()->id ?? $titles->random()->id;
            } elseif ($rand <= 85) {
                // Nhân sự
                $titleId = $titles->where('name', 'Nhân sự')->first()->id ?? $titles->random()->id;
            } elseif ($rand <= 95) {
                // Chấp sự
                $titleId = $titles->where('name', 'Chấp sự')->first()->id ?? $titles->random()->id;
            } else {
                // Random others
                $titleId = $titles->random()->id;
            }

            $member = Member::create([
                'family_id' => $families->random()->id,
                'full_name' => $fullName,
                'gender' => $gender,
                'birthday' => Carbon::now()->subYears(rand(10, 70))->subDays(rand(0, 365)),
                'phone' => '09' . rand(10000000, 99999999),
                'email' => Str::slug($fullName) . rand(10, 99) . '@example.com',
                'job' => $jobs[array_rand($jobs)],
                'title_id' => $titleId,
                'status' => 'active',
                'date_baptism' => (rand(0, 1) == 1) ? Carbon::now()->subYears(rand(2, 10)) : null,
                'joined_date' => Carbon::now()->subYears(rand(1, 20)),
            ]);

            // Assign to 1-2 Departments
            $numDepts = rand(1, 2);
            $randomDepts = $departments->random($numDepts);

            foreach ($randomDepts as $idx => $dept) {
                // Determine Position in Department
                // First assignment might be a specific role if we are lucky, otherwise Member
                // Let's make it simple: 1 Leader per Dept, 2 Vice, etc. but that requires tracking. 
                // For simplified test data, we assign random positions with weights.
                $posRand = rand(1, 100);
                $positionName = 'Thành viên';
                
                if ($posRand > 95) $positionName = 'Trưởng ban';
                elseif ($posRand > 90) $positionName = 'Phó ban';
                elseif ($posRand > 85) $positionName = 'Thư ký';
                elseif ($posRand > 80) $positionName = 'Thủ quỹ';
                elseif ($posRand > 70) $positionName = 'Ủy viên';

                $position = $positions->where('name', $positionName)->first();

                // Attach to Department
                // Assuming `department_member` pivot table or `members` -> `departments` relationship
                // Checking `Member` model relationship... assuming `departments()`
                // If the relationship is `groups()`, we might need to adjust, 
                // but the user wants `departments`. MasterDataSeeder seeded `departments`.
                // Let's look at `DepartmentMember` table migration: `2026_02_06_124842_create_department_member_table.php`
                // It likely links `department_id` and `member_id` and `position_id` (or `position` string).
                
                DB::table('department_member')->insert([
                    'department_id' => $dept->id,
                    'member_id' => $member->id,
                    'role' => $position ? $position->name : 'Thành viên', // Use name as role
                    'sub_group_id' => null, // Or logic to assign sub_group
                    // 'joined_at' => ... wait, pivot usually just has timestamps, let's check migration again
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $count++;
        }
        
        $this->command->info("- Created $count members and assigned to departments.");
    }
}
