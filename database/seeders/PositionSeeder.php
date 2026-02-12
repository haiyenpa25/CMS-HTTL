<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Modules\Organization\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            ['name' => 'Trưởng ban', 'slug' => 'truong-ban', 'level' => 1, 'description' => 'Người đứng đầu ban'],
            ['name' => 'Phó ban', 'slug' => 'pho-ban', 'level' => 2, 'description' => 'Phó ban hỗ trợ trưởng ban'],
            ['name' => 'Thư ký', 'slug' => 'thu-ky', 'level' => 3, 'description' => 'Quản lý hành chính và văn thư'],
            ['name' => 'Thủ quỹ', 'slug' => 'thu-quy', 'level' => 4, 'description' => 'Quản lý tài chính'],
            ['name' => 'Giáo viên', 'slug' => 'giao-vien', 'level' => 5, 'description' => 'Giảng dạy và hướng dẫn'],
            ['name' => 'Thành viên', 'slug' => 'thanh-vien', 'level' => 10, 'description' => 'Thành viên thường'],
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate(
                ['slug' => $position['slug']],
                $position
            );
        }
    }
}
