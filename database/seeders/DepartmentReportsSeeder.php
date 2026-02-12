<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Reports\Models\DepartmentReport;
use App\Modules\Reports\Models\DepartmentActivity;
use App\Modules\Reports\Models\DepartmentWeeklyStat;
use App\Modules\Reports\Models\DepartmentVisitRecord;
use App\Modules\Organization\Models\Department;
use App\Modules\Membership\Models\Member;
use Carbon\Carbon;

class DepartmentReportsSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();
        
        if ($departments->isEmpty()) {
            $this->command->warn('No departments found. Please seed departments first.');
            return;
        }

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        foreach ($departments->take(3) as $department) {
            // Create report for current month
            $report = DepartmentReport::create([
                'department_id' => $department->id,
                'year' => $currentYear,
                'month' => $currentMonth,
                'created_by' => 1,
                'total_attendance' => rand(1200, 1500),
                'total_donations' => rand(12000000, 16000000),
                'visits_completed' => rand(35, 45),
                'visits_total' => rand(50, 60),
                'new_members' => rand(10, 20),
                'attendance_change_percent' => rand(-5, 10) + (rand(0, 99) / 100),
                'donations_change_percent' => rand(-3, 8) + (rand(0, 99) / 100),
                'general_comments' => 'Sự tham gia của giới trẻ đang có xu hướng tăng mạnh. Các buổi thông công mang lại hiệu quả gắn kết cao. Cần duy trì chất lượng bài học Kinh Thánh hàng tuần.',
                'suggestions' => 'Nâng cấp hệ thống âm thanh cho phòng nhóm nhỏ. Bổ sung thêm ngân sách cho công tác cứu trợ khu vực vùng sâu.',
                'prayer_requests' => 'Yêu cầu cầu nguyện khẩn thiết cho gia đình anh chị Nguyễn Văn A (vừa mất người thân) và sự chuẩn bị cho đại lễ Phục sinh sắp tới cũng như kế hoạch hè.',
                'prayer_topics' => ['Gia đình khó khăn', 'Ban điều hành mới', 'Công tác truyền giáo'],
                'status' => 'approved',
                'submitted_at' => now(),
                'approved_at' => now(),
                'approved_by' => 1,
            ]);

            // Create weekly stats
            for ($week = 1; $week <= 4; $week++) {
                DepartmentWeeklyStat::create([
                    'report_id' => $report->id,
                    'week_number' => $week,
                    'attendance' => rand(300, 600),
                    'donations' => rand(1000000, 6000000),
                ]);
            }

            // Create activities
            $activities = [
                ['name' => 'Thông công Ban ngành', 'desc' => 'Buổi họp mặt kết thân và cầu nguyện chung.'],
                ['name' => 'Học Kinh Thánh', 'desc' => 'Nghiên cứu sách Rô-ma, phân đoạn 1-5.'],
                ['name' => 'Huấn luyện Lãnh đạo', 'desc' => 'Kỹ năng điều phối và chăm sóc tín hữu.'],
                ['name' => 'Truyền giảng Nội bộ', 'desc' => 'Mời thân hữu tham gia giao lưu âm nhạc.'],
            ];

            foreach ($activities as $index => $activity) {
                DepartmentActivity::create([
                    'report_id' => $report->id,
                    'activity_date' => Carbon::create($currentYear, $currentMonth, 7 + ($index * 7)),
                    'name' => $activity['name'],
                    'description' => $activity['desc'],
                    'donations_received' => rand(1000000, 6000000),
                    'attendance' => rand(100, 300),
                    'location' => 'Hội thánh',
                ]);
            }

            // Create visit records
            $members = Member::inRandomOrder()->take(5)->get();
            $visitTypes = ['sick', 'new_believer', 'follow_up', 'encouragement'];
            
            foreach ($members as $index => $member) {
                DepartmentVisitRecord::create([
                    'report_id' => $report->id,
                    'member_id' => $member->id,
                    'visit_date' => Carbon::create($currentYear, $currentMonth, 10 + ($index * 3)),
                    'visit_type' => $visitTypes[array_rand($visitTypes)],
                    'notes' => 'Thăm viếng và động viên',
                    'status' => $index < 3 ? 'completed' : 'pending',
                ]);
            }

            // Create reports for previous 3 months for comparison
            for ($i = 1; $i <= 3; $i++) {
                $prevDate = Carbon::create($currentYear, $currentMonth)->subMonths($i);
                
                DepartmentReport::create([
                    'department_id' => $department->id,
                    'year' => $prevDate->year,
                    'month' => $prevDate->month,
                    'created_by' => 1,
                    'total_attendance' => rand(1100, 1400),
                    'total_donations' => rand(11000000, 15000000),
                    'visits_completed' => rand(30, 40),
                    'visits_total' => rand(50, 60),
                    'new_members' => rand(8, 15),
                    'status' => 'approved',
                    'submitted_at' => $prevDate,
                    'approved_at' => $prevDate,
                    'approved_by' => 1,
                ]);
            }
        }

        $this->command->info('Department reports seeded successfully!');
    }
}
