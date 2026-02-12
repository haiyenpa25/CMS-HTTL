<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            ['name' => 'Mục sư', 'level' => 1, 'description' => 'Mục sư quản nhiệm hoặc phụ tá'],
            ['name' => 'Mục sư Nhiệm chức', 'level' => 2, 'description' => 'Mục sư nhiệm chức'],
            ['name' => 'Truyền đạo', 'level' => 3, 'description' => 'Thầy truyền đạo'],
            ['name' => 'Nữ Truyền giáo', 'level' => 3, 'description' => 'Nữ truyền giáo'],
            ['name' => 'Chấp sự', 'level' => 4, 'description' => 'Chấp sự đương nhiệm'],
            ['name' => 'Nhân sự', 'level' => 5, 'description' => 'Nhân sự nòng cốt'],
            ['name' => 'Tín hữu', 'level' => 6, 'description' => 'Tín hữu chính thức'],
            ['name' => 'Thân hữu', 'level' => 7, 'description' => 'Người mới tìm hiểu/Chưa tin Chúa'],
        ];

        foreach ($titles as $title) {
            DB::table('titles')->updateOrInsert(
                ['name' => $title['name']],
                array_merge($title, [
                    'slug' => Str::slug($title['name']),
                    'created_at' => Carbon::now(), 
                    'updated_at' => Carbon::now()
                ])
            );
        }
    }
}
