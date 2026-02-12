<?php

namespace Database\Seeders;

use App\Models\Family;
use App\Models\Group;
use App\Models\Member;
use App\Models\Title;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Tạo các ban (Groups)
            $groups = $this->createGroups();
            
            // Tạo các gia đình
            $families = $this->createFamilies();
            
            // Tạo members
            $members = $this->createMembers($families);
            
            // Phân bổ members vào các ban
            $this->assignMembersToGroups($members, $groups);

            DB::commit();
            
            $this->command->info('✅ Đã tạo thành công dữ liệu test!');
            $this->command->info("   - {$groups->count()} ban");
            $this->command->info("   - {$families->count()} gia đình");
            $this->command->info("   - {$members->count()} thành viên");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Lỗi: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createGroups()
    {
        $this->command->info('Đang tạo các ban...');
        
        $groupsData = [
            // Ban Lãnh đạo
            [
                'name' => 'Ban Chấp Hành',
                'type' => 'Lãnh đạo',
                'description' => 'Ban lãnh đạo chung của hội thánh',
                'features' => ['attendance', 'scheduling', 'inventory', 'visits']
            ],
            [
                'name' => 'Ban Truyền Giáo',
                'type' => 'Lãnh đạo',
                'description' => 'Phụ trách công tác truyền giáo',
                'features' => ['attendance', 'scheduling', 'visits']
            ],
            
            // Ban Sinh hoạt
            [
                'name' => 'Thanh Niên',
                'type' => 'Sinh hoạt',
                'description' => 'Ban sinh hoạt thanh niên',
                'features' => ['attendance', 'scheduling', 'visits']
            ],
            [
                'name' => 'Thiếu Nhi',
                'type' => 'Sinh hoạt',
                'description' => 'Ban sinh hoạt thiếu nhi',
                'features' => ['attendance']
            ],
            [
                'name' => 'Gia Đình',
                'type' => 'Sinh hoạt',
                'description' => 'Ban sinh hoạt gia đình',
                'features' => ['attendance', 'scheduling', 'visits']
            ],
            
            // Ban Mục vụ
            [
                'name' => 'Ban Thờ Phượng',
                'type' => 'Mục vụ',
                'description' => 'Phụ trách công tác thờ phượng',
                'features' => ['attendance', 'scheduling', 'inventory']
            ],
            [
                'name' => 'Ban Âm Nhạc',
                'type' => 'Mục vụ',
                'description' => 'Phụ trách công tác âm nhạc',
                'features' => ['attendance', 'scheduling']
            ],
            [
                'name' => 'Ban Tiếp Tân',
                'type' => 'Mục vụ',
                'description' => 'Phụ trách công tác tiếp tân',
                'features' => ['attendance']
            ],
            [
                'name' => 'Ban Đoàn Khế',
                'type' => 'Mục vụ',
                'description' => 'Phụ trách công tác đoàn khế',
                'features' => ['attendance', 'scheduling', 'visits']
            ],
        ];

        $groups = collect();
        foreach ($groupsData as $data) {
            $groups->push(Group::create($data));
        }

        return $groups;
    }

    private function createFamilies()
    {
        $this->command->info('Đang tạo các gia đình...');
        
        $familiesData = [
            ['name' => 'Gia đình Nguyễn Văn A', 'address' => '123 Lê Lợi', 'ward' => 'Phường 1', 'district' => 'Quận 1', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Trần Thị B', 'address' => '456 Nguyễn Huệ', 'ward' => 'Phường 2', 'district' => 'Quận 1', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Lê Văn C', 'address' => '789 Hai Bà Trưng', 'ward' => 'Phường 3', 'district' => 'Quận 3', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Phạm Thị D', 'address' => '321 Trần Hưng Đạo', 'ward' => 'Phường 4', 'district' => 'Quận 5', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Hoàng Văn E', 'address' => '654 Lý Thường Kiệt', 'ward' => 'Phường 5', 'district' => 'Quận 10', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Vũ Thị F', 'address' => '987 Võ Văn Tần', 'ward' => 'Phường 6', 'district' => 'Quận 3', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Đặng Văn G', 'address' => '147 Pasteur', 'ward' => 'Phường 7', 'district' => 'Quận 1', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Bùi Thị H', 'address' => '258 Cách Mạng Tháng 8', 'ward' => 'Phường 8', 'district' => 'Quận 10', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Đỗ Văn I', 'address' => '369 Điện Biên Phủ', 'ward' => 'Phường 9', 'district' => 'Quận Bình Thạnh', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Ngô Thị K', 'address' => '741 Xô Viết Nghệ Tĩnh', 'ward' => 'Phường 10', 'district' => 'Quận Bình Thạnh', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Mai Văn L', 'address' => '852 Hoàng Văn Thụ', 'ward' => 'Phường 11', 'district' => 'Quận Tân Bình', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Dương Thị M', 'address' => '963 Cộng Hòa', 'ward' => 'Phường 12', 'district' => 'Quận Tân Bình', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Lý Văn N', 'address' => '159 Lạc Long Quân', 'ward' => 'Phường 13', 'district' => 'Quận 11', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Phan Thị O', 'address' => '357 Âu Cơ', 'ward' => 'Phường 14', 'district' => 'Quận Tân Phú', 'province' => 'TP.HCM'],
            ['name' => 'Gia đình Trịnh Văn P', 'address' => '753 Tân Sơn Nhì', 'ward' => 'Phường 15', 'district' => 'Quận Tân Phú', 'province' => 'TP.HCM'],
        ];

        $families = collect();
        foreach ($familiesData as $data) {
            $families->push(Family::create($data));
        }

        return $families;
    }

    private function createMembers($families)
    {
        $this->command->info('Đang tạo 50 thành viên...');
        
        $titles = Title::all();
        
        $firstNames = [
            'male' => ['Văn', 'Đức', 'Minh', 'Hoàng', 'Quang', 'Tuấn', 'Hùng', 'Dũng', 'Thành', 'Phúc', 'Khoa', 'Long', 'Nam', 'Tài', 'Trí'],
            'female' => ['Thị', 'Thu', 'Hương', 'Lan', 'Mai', 'Hoa', 'Linh', 'Ngọc', 'Phương', 'Trang', 'Anh', 'Chi', 'Hà', 'Nhung', 'Vy']
        ];
        
        $lastNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Ngô', 'Dương', 'Lý', 'Phan', 'Trịnh'];
        
        $middleNames = [
            'male' => ['Văn', 'Đức', 'Minh', 'Quốc', 'Hữu', 'Công', 'Xuân', 'Thanh', 'Ngọc', 'Anh'],
            'female' => ['Thị', 'Thu', 'Ngọc', 'Thanh', 'Kim', 'Hồng', 'Phương', 'Diệu', 'Bích', 'Như']
        ];
        
        $jobs = [
            ['name' => 'Giáo viên', 'company' => 'Trường THPT Lê Quý Đôn'],
            ['name' => 'Kỹ sư', 'company' => 'Công ty TNHH ABC'],
            ['name' => 'Bác sĩ', 'company' => 'Bệnh viện Chợ Rẫy'],
            ['name' => 'Nhân viên văn phòng', 'company' => 'Công ty XYZ'],
            ['name' => 'Kinh doanh', 'company' => 'Công ty DEF'],
            ['name' => 'Lập trình viên', 'company' => 'Công ty Phần mềm GHI'],
            ['name' => 'Kế toán', 'company' => 'Công ty Kiểm toán JKL'],
            ['name' => 'Sinh viên', 'company' => 'Đại học Khoa học Tự nhiên'],
            ['name' => 'Học sinh', 'company' => 'Trường THPT'],
            ['name' => 'Nội trợ', 'company' => null],
        ];

        $spiritualGifts = [
            ['Giảng dạy'], ['Truyền giáo'], ['Cầu nguyện'], ['Ca hát'], ['Chơi nhạc'],
            ['Phục vụ'], ['Khuyến khích'], ['Cho đi'], ['Lãnh đạo'], ['Thương xót']
        ];

        $members = collect();
        
        for ($i = 1; $i <= 50; $i++) {
            $gender = $i % 2 == 0 ? 'Nam' : 'Nữ';
            $genderKey = $gender == 'Nam' ? 'male' : 'female';
            
            $lastName = $lastNames[array_rand($lastNames)];
            $middleName = $middleNames[$genderKey][array_rand($middleNames[$genderKey])];
            $firstName = $firstNames[$genderKey][array_rand($firstNames[$genderKey])];
            $fullName = "{$lastName} {$middleName} {$firstName}";
            
            $age = rand(15, 65);
            $birthday = now()->subYears($age)->subDays(rand(0, 365));
            
            $job = $jobs[array_rand($jobs)];
            
            $member = Member::create([
                'family_id' => $families->random()->id,
                'title_id' => $titles->random()->id,
                'full_name' => $fullName,
                'phone' => '0' . rand(900000000, 999999999),
                'email' => strtolower(str_replace(' ', '', $firstName . $lastName)) . $i . '@gmail.com',
                'gender' => $gender,
                'birthday' => $birthday,
                'blood_group' => ['A', 'B', 'O', 'AB'][array_rand(['A', 'B', 'O', 'AB'])],
                'job' => $job,
                'is_married' => $age >= 25 ? (rand(0, 100) > 40) : false,
                'date_faith' => $birthday->copy()->addYears(rand(5, 15)),
                'date_baptism' => rand(0, 100) > 20 ? $birthday->copy()->addYears(rand(10, 20)) : null,
                'joined_date' => now()->subYears(rand(1, 10)),
                'referred_by' => rand(0, 100) > 50 ? 'Bạn bè' : 'Gia đình',
                'spiritual_gifts' => $spiritualGifts[array_rand($spiritualGifts)],
                'status' => ['active', 'active', 'active', 'inactive', 'weak'][array_rand(['active', 'active', 'active', 'inactive', 'weak'])],
                'last_visited_at' => now()->subDays(rand(0, 30)),
            ]);
            
            $members->push($member);
        }

        return $members;
    }

    private function assignMembersToGroups($members, $groups)
    {
        $this->command->info('Đang phân bổ thành viên vào các ban...');
        
        $roles = ['Trưởng ban', 'Phó ban', 'Thư ký', 'Thành viên', 'Thành viên'];
        $subGroups = ['Tổ 1', 'Tổ 2', 'Tổ 3', null, null];
        
        // Phân bổ mỗi member vào 1-3 ban ngẫu nhiên
        foreach ($members as $member) {
            $numberOfGroups = rand(1, 3);
            $selectedGroups = $groups->random($numberOfGroups);
            
            foreach ($selectedGroups as $index => $group) {
                // Trưởng ban và Phó ban chỉ có 1 người mỗi ban
                $role = $index == 0 && rand(0, 100) > 80 ? $roles[array_rand($roles)] : 'Thành viên';
                
                $member->groups()->attach($group->id, [
                    'role' => $role,
                    'sub_group' => $subGroups[array_rand($subGroups)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
