<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Modules\Membership\Models\Title;
use App\Modules\Organization\Models\Position;
use App\Modules\Membership\Models\VisitCategory;
use App\Modules\Assets\Models\AssetCategory;
use App\Modules\Organization\Models\Department; // Using Department as the main organizational unit per plan

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Start seeding Master Data...');

        Schema::disableForeignKeyConstraints();
        
        // 1. Titles (Danh xưng)
        $this->seedTitles();

        // 2. Positions (Chức vụ trong ban)
        $this->seedPositions();

        // 3. Visit Categories (Mục đích thăm viếng)
        $this->seedVisitCategories();

        // 4. Asset Categories (Danh mục tài sản)
        $this->seedAssetCategories();

        // 5. Departments (Cơ cấu tổ chức - Lấy từ dữ liệu mẫu hiện có)
        $this->seedDepartments();

        Schema::enableForeignKeyConstraints();
        
        $this->command->info('Master Data seeded successfully!');
    }

    private function seedTitles()
    {
        DB::table('titles')->truncate();
        
        $titles = [
            // Member Titles
            ['name' => 'Chấp sự', 'type' => 'member', 'level' => 3, 'description' => 'Chấp sự đương nhiệm'],
            ['name' => 'Thư ký Hội thánh', 'type' => 'member', 'level' => 3, 'description' => 'Thư ký Hội thánh'],
            ['name' => 'Thủ quỹ', 'type' => 'member', 'level' => 3, 'description' => 'Thủ quỹ Hội thánh'],
            ['name' => 'Phó Thủ quỹ', 'type' => 'member', 'level' => 3, 'description' => 'Phó Thủ quỹ Hội thánh'],
            ['name' => 'Ủy viên', 'type' => 'member', 'level' => 4, 'description' => 'Ủy viên Ban Chấp sự'],
            ['name' => 'Nhân sự', 'type' => 'member', 'level' => 5, 'description' => 'Nhân sự phục vụ'],
            ['name' => 'Tín hữu', 'type' => 'member', 'level' => 6, 'description' => 'Tín hữu chính thức'],
            ['name' => 'Thân hữu', 'type' => 'member', 'level' => 7, 'description' => 'Người mới tìm hiểu/Chưa tin Chúa'],
            
            // Speaker Titles (Also stored here for reference/usage)
            ['name' => 'Mục sư', 'type' => 'clergy', 'level' => 1, 'description' => 'Chức danh Mục sư'],
            ['name' => 'Mục sư Nhậm chức', 'type' => 'clergy', 'level' => 1, 'description' => 'Chức danh Mục sư Nhậm chức'], // "Nhiệm chức" corrected to "Nhậm chức" or kept as "Nhiệm chức" based on common usage? User wrote "Mục sư nhiệm chức". Keeping user's text.
            ['name' => 'Mục sư Nhiệm chức', 'type' => 'clergy', 'level' => 1, 'description' => 'Chức danh Mục sư Nhiệm chức'],
            ['name' => 'Truyền đạo', 'type' => 'clergy', 'level' => 2, 'description' => 'Chức danh Truyền đạo'],
            ['name' => 'Nữ Truyền Đạo', 'type' => 'clergy', 'level' => 2, 'description' => 'Chức danh Nữ Truyền Đạo'],
            ['name' => 'Thầy sinh viên', 'type' => 'clergy', 'level' => 2, 'description' => 'Sinh viên Thần học (Nam)'],
            ['name' => 'Cô sinh viên', 'type' => 'clergy', 'level' => 2, 'description' => 'Sinh viên Thần học (Nữ)'],
        ];

        foreach ($titles as $title) {
            DB::table('titles')->insert([
                'name' => $title['name'],
                'slug' => Str::slug($title['name']),
                'level' => $title['level'],
                'description' => $title['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('- Titles seeded.');
    }

    private function seedPositions()
    {
        DB::table('positions')->truncate();

        $positions = [
            ['name' => 'Trưởng ban', 'level' => 1, 'description' => 'Người đứng đầu ban'],
            ['name' => 'Phó ban', 'level' => 2, 'description' => 'Phó ban hỗ trợ trưởng ban'],
            ['name' => 'Thư ký', 'level' => 3, 'description' => 'Quản lý hành chính và văn thư của ban'],
            ['name' => 'Thủ quỹ', 'level' => 4, 'description' => 'Quản lý tài chính của ban'],
            ['name' => 'Ủy viên', 'level' => 5, 'description' => 'Ủy viên ban điều hành'],
            ['name' => 'Thành viên', 'level' => 10, 'description' => 'Thành viên ban'],
        ];

        foreach ($positions as $pos) {
            DB::table('positions')->insert([
                'name' => $pos['name'],
                'slug' => Str::slug($pos['name']),
                'level' => $pos['level'],
                'description' => $pos['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('- Positions seeded.');
    }

    private function seedVisitCategories()
    {
        DB::table('visit_categories')->truncate();

        $categories = [
            ['name' => 'Thăm thường', 'color' => '#3B82F6', 'icon' => '🏠'],
            ['name' => 'Thăm bệnh', 'color' => '#EF4444', 'icon' => '🏥'],
            ['name' => 'Thăm mừng', 'color' => '#10B981', 'icon' => '🎉'],
            ['name' => 'Thăm chia buồn', 'color' => '#6B7280', 'icon' => '🕊️'],
            ['name' => 'Thăm khuyến khích', 'color' => '#F59E0B', 'icon' => '💪'],
            ['name' => 'Thăm khẩn cấp', 'color' => '#DC2626', 'icon' => '🚨'],
        ];

        foreach ($categories as $index => $cat) {
            DB::table('visit_categories')->insert([
                'name' => $cat['name'],
                'color' => $cat['color'],
                'icon' => $cat['icon'],
                'sort_order' => $index + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('- Visit Categories seeded.');
    }

    private function seedAssetCategories()
    {
        DB::table('asset_categories')->truncate();

        $categories = [
            ['name' => 'Thiết bị Âm Thanh', 'code' => 'AT'],
            ['name' => 'Thiết bị Trình Chiếu', 'code' => 'TC'],
            ['name' => 'Thiết bị Truyền Thông', 'code' => 'TT'],
            ['name' => 'Thiết bị Ban Đàn', 'code' => 'BD'], // corrected case
            ['name' => 'Thiết bị Phòng Nhóm', 'code' => 'PN'],
            ['name' => 'Thiết bị Ẩm Thực', 'code' => 'AMC'],
            ['name' => 'Thiết bị Điện', 'code' => 'DIEN'],
            ['name' => 'Thiết bị Mạng', 'code' => 'NET'],
        ];

        foreach ($categories as $cat) {
            DB::table('asset_categories')->insert([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                // 'code' removed as not in schema
                // 'is_active' removed as not in schema
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('- Asset Categories seeded.');
    }

    private function seedDepartments()
    {
        DB::table('departments')->truncate();
        
        $departments = [
            // KHỐI LÃNH ĐẠO
            [
                'name' => 'Ban Chấp sự',
                'type' => 'Lãnh đạo',
                'description' => 'Ban điều hành chung công việc Chúa',
                'features' => json_encode(['attendance', 'scheduling', 'visits'])
            ],
            [
                'name' => 'Ban Trị Sự',
                'type' => 'Lãnh đạo',
                'description' => 'Ban lo công tác trị sự',
                'features' => json_encode(['attendance', 'scheduling'])
            ],

            // KHỐI SINH HOẠT
            [
                'name' => 'Ban Trung Lão',
                'type' => 'Sinh hoạt',
                'description' => 'Ban sinh hoạt độ tuổi trung lão',
                'features' => json_encode(['attendance', 'scheduling', 'visits'])
            ],
            [
                'name' => 'Ban Thanh Tráng',
                'type' => 'Sinh hoạt',
                'description' => 'Ban sinh hoạt độ tuổi thanh tráng',
                'features' => json_encode(['attendance', 'scheduling', 'visits'])
            ],
            [
                'name' => 'Ban Thanh Niên',
                'type' => 'Sinh hoạt',
                'description' => 'Ban sinh hoạt thanh niên',
                'features' => json_encode(['attendance', 'scheduling', 'visits'])
            ],
            [
                'name' => 'Ban Thiếu Nhi',
                'type' => 'Sinh hoạt',
                'description' => 'Ban sinh hoạt thiếu nhi',
                'features' => json_encode(['attendance'])
            ],

            // KHỐI MỤC VỤ
            [
                'name' => 'Ban Cơ Đốc Giáo Dục',
                'type' => 'Mục vụ',
                'description' => 'Lo về việc học Lời Chúa',
                'features' => json_encode(['attendance', 'scheduling'])
            ],
            [
                'name' => 'Ban Truyền Giảng',
                'type' => 'Mục vụ',
                'description' => 'Lo công tác truyền giảng Tin Lành',
                'features' => json_encode(['attendance', 'scheduling', 'visits'])
            ],
            [
                'name' => 'Ban Chứng Đạo - Chăm Sóc Tân Tín hữu', // Combined Name
                'type' => 'Mục vụ',
                'description' => 'Chăm sóc người mới và chứng đạo',
                'features' => json_encode(['visits', 'attendance'])
            ],
            [
                'name' => 'Ban Kỹ Thuật',
                'type' => 'Mục vụ',
                'description' => 'Phụ trách kỹ thuật chung',
                'features' => json_encode(['inventory', 'scheduling', 'attendance'])
            ],
            [
                'name' => 'Ban Âm Thanh',
                'type' => 'Mục vụ',
                'description' => 'Phụ trách âm thanh',
                'features' => json_encode(['inventory', 'scheduling', 'attendance'])
            ],
            [
                'name' => 'Ban Máy Chiếu',
                'type' => 'Mục vụ',
                'description' => 'Phụ trách trình chiếu',
                'features' => json_encode(['inventory', 'attendance'])
            ],
            [
                'name' => 'Ban Truyền Thông',
                'type' => 'Mục vụ',
                'description' => 'Phụ trách media và truyền thông',
                'features' => json_encode(['inventory', 'attendance'])
            ],
            [
                'name' => 'Ban Nhạc Cụ', // Assuming this is "Ban Nhạc Cụ" mentioned as "Ban đàn" in assets, but here it's "Ban Nhạc Cụ" in list
                'type' => 'Mục vụ',
                'description' => 'Ban đàn và nhạc cụ',
                'features' => json_encode(['inventory', 'scheduling', 'attendance'])
            ],
            [
                'name' => 'Ban Kết Nối',
                'type' => 'Mục vụ',
                'description' => 'Kết nối thành viên',
                'features' => json_encode(['attendance', 'visits'])
            ],
            [
                'name' => 'Ban Khánh Tiết',
                'type' => 'Mục vụ',
                'description' => 'Trang trí và khánh tiết',
                'features' => json_encode(['inventory', 'attendance'])
            ],
            [
                'name' => 'Ban Hậu Cần',
                'type' => 'Mục vụ',
                'description' => 'Lo công tác hậu cần',
                'features' => json_encode(['inventory', 'attendance'])
            ],
            [
                'name' => 'Ban Cầu Nguyện',
                'type' => 'Mục vụ',
                'description' => 'Ban cầu nguyện',
                'features' => json_encode(['attendance', 'visits'])
            ],
            [
                'name' => 'Ban Tiếp Tân - Trật Tự',
                'type' => 'Mục vụ',
                'description' => 'Đón tiếp và giữ trật tự',
                'features' => json_encode(['attendance'])
            ],
            [
                'name' => 'Ban Tương Trợ',
                'type' => 'Mục vụ',
                'description' => 'Giúp đỡ khó khăn',
                'features' => json_encode(['visits', 'attendance'])
            ],
            [
                'name' => 'Ban Thăm Viếng',
                'type' => 'Mục vụ',
                'description' => 'Chuyên trách thăm viếng',
                'features' => json_encode(['visits', 'attendance'])
            ],
            [
                'name' => 'Ban Hát Thờ Phượng',
                'type' => 'Mục vụ',
                'description' => 'Ban hát dẫn thờ phượng',
                'features' => json_encode(['scheduling', 'attendance'])
            ],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->insert(array_merge($dept, [
                'status' => 'active', // Adding default status
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
        $this->command->info('- Departments seeded.');
    }
}
