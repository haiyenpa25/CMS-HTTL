<?php

namespace App\Modules\Reports\Livewire;

use Livewire\Component;
use App\Modules\Reports\Models\DepartmentReport;
use App\Modules\Organization\Models\Department;
use Carbon\Carbon;

class DepartmentReportDashboard extends Component
{
    public $selectedDepartmentId;
    public $selectedYear;
    public $selectedMonth;
    public $report;
    
    public function mount()
    {
        $this->selectedYear = Carbon::now()->year;
        $this->selectedMonth = Carbon::now()->month;
        
        // Get first department as default
        $firstDept = Department::first();
        $this->selectedDepartmentId = $firstDept?->id;
        
        $this->loadReport();
    }

    public $editMode = false;
    public $formData = [];
    public $nextMonthTasks = [];
    public $newActivity = [
        'activity_date' => '',
        'name' => '',
        'description' => '',
        'donations_received' => 0,
        'attendance' => 0,
    ];
    public $newTask = [
        'task_name' => '',
        'description' => '',
        'scheduled_date' => '',
        'location' => '',
    ];

    protected $rules = [
        'formData.general_comments' => 'nullable|string',
        'formData.suggestions' => 'nullable|string',
        'formData.prayer_requests' => 'nullable|string',
    ];

    public function updatedSelectedDepartmentId()
    {
        $this->loadReport();
    }

    public function updatedSelectedMonth()
    {
        $this->loadReport();
    }

    public function loadReport()
    {
        if (!$this->selectedDepartmentId) {
            return;
        }

        $this->report = DepartmentReport::with([
            'department',
            'activities',
            'weeklyStats',
            'visitRecords.member'
        ])
        ->where('department_id', $this->selectedDepartmentId)
        ->where('year', $this->selectedYear)
        ->where('month', $this->selectedMonth)
        ->first();

        // If no report exists, create a draft
        if (!$this->report) {
            $this->report = DepartmentReport::create([
                'department_id' => $this->selectedDepartmentId,
                'year' => $this->selectedYear,
                'month' => $this->selectedMonth,
                'created_by' => auth()->id(),
                'status' => 'draft',
            ]);
            
            // Auto-sync data for new report
            $this->syncReportData();
        }

        $this->formData = [
            'general_comments' => $this->report->general_comments,
            'suggestions' => $this->report->suggestions,
            'prayer_requests' => $this->report->prayer_requests,
        ];
        
        $this->loadTasks();
    }

    public function loadTasks()
    {
        // Load tasks for this report (using department_next_month_tasks table)
        // We filter by the report's month/year context implies tasks PLANNED in this report 
        // usually meant for NEXT month.
        // But the table has `month`, `year`. 
        // If this report is for October, Next Month Tasks are for November?
        // Let's assume the table stores the target month/year.
        
        $targetDate = Carbon::create($this->selectedYear, $this->selectedMonth)->addMonth();
        
        $this->nextMonthTasks = \DB::table('department_next_month_tasks')
            ->where('department_id', $this->selectedDepartmentId)
            ->where('year', $targetDate->year)
            ->where('month', $targetDate->month)
            ->get();
    }

    public function toggleEditMode()
    {
        $this->editMode = !$this->editMode;
        if ($this->editMode && !$this->report) {
            $this->loadReport();
        }
    }

    public function syncReportData()
    {
        if (!$this->report) return;

        // 1. Sync Attendance from AttendanceSession
        // Get all sessions for this department in this month
        $sessions = \App\Modules\Attendance\Models\AttendanceSession::where('department_id', $this->selectedDepartmentId)
            ->whereYear('date', $this->selectedYear)
            ->whereMonth('date', $this->selectedMonth)
            ->withSum('summaries', 'total_present')
            ->get();
            
        // Also include Sunday Services? Usually Report is for Department Activities.
        // If Sunday Service, we might check if this department has attendance records?
        // For now, only specific Department Sessions.
        
        $totalAttendance = $sessions->sum('summaries_sum_total_present'); // aggregate from summaries
        // Fallback if summaries not used:
        if ($totalAttendance == 0) {
             // Try manual counts
             $totalAttendance = $sessions->sum('manual_count');
        }

        $this->report->total_attendance = $totalAttendance;

        // 2. Sync Visits (Count MemberVisits for department members)
        // This is tricky. Let's just count 'department_visit_records' if we want manual control.
        // Or sync from MemberVisit.
        // Let's stick to what we have in the DB for now to avoid overwriting manual work unless explicitly asked.
        // But we can update the 'weekly stats' based on sessions.
        
        // Calculate Weekly Stats from Sessions
        $this->report->weeklyStats()->delete();
        $sessions->groupBy(function($date) {
            return Carbon::parse($date->date)->weekOfMonth;
        })->each(function($weeklySessions, $weekNum) {
            $this->report->weeklyStats()->create([
                'week_number' => $weekNum,
                'attendance' => $weeklySessions->sum(fn($s) => $s->manual_count + $s->summaries_sum_total_present)
            ]);
        });
        
        // Update Totals from activities
        $activitiesTotalDonation = $this->report->activities()->sum('donations_received');
        $this->report->total_donations = $activitiesTotalDonation;

        $this->report->save();
        $this->loadReport(); // refresh
        
        session()->flash('success', 'Đã đồng bộ dữ liệu từ hệ thống.');
    }

    public function saveReport()
    {
        $this->validate();
        
        if ($this->report) {
            $this->report->update($this->formData);
            $this->editMode = false;
            session()->flash('success', 'Đã lưu báo cáo thành công.');
        }
    }

    public function addActivity()
    {
        $this->validate([
            'newActivity.name' => 'required|string',
            'newActivity.activity_date' => 'required|date',
        ]);

        $this->report->activities()->create($this->newActivity);
        
        // Reset form
        $this->newActivity = [
            'activity_date' => '',
            'name' => '',
            'description' => '',
            'donations_received' => 0,
            'attendance' => 0,
        ];
        
        $this->syncReportData(); // Re-calc totals
    }

    public function deleteActivity($activityId)
    {
        $this->report->activities()->where('id', $activityId)->delete();
        $this->syncReportData();
    }

    public function addTask()
    {
        $this->validate([
            'newTask.task_name' => 'required|string',
        ]);

        $targetDate = Carbon::create($this->selectedYear, $this->selectedMonth)->addMonth();

        \DB::table('department_next_month_tasks')->insert([
            'department_id' => $this->selectedDepartmentId,
            'year' => $targetDate->year,
            'month' => $targetDate->month,
            'week_number' => 1, // Default
            'task_name' => $this->newTask['task_name'],
            'description' => $this->newTask['description'],
            'scheduled_date' => $this->newTask['scheduled_date'] ?: null,
            'location' => $this->newTask['location'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->newTask = [
            'task_name' => '',
            'description' => '',
            'scheduled_date' => '',
            'location' => '',
        ];
        
        $this->loadTasks();
    }

    public function deleteTask($taskId)
    {
        \DB::table('department_next_month_tasks')->where('id', $taskId)->delete();
        $this->loadTasks();
    }


    public function getMonthlyComparisons()
    {
        if (!$this->selectedDepartmentId) {
            return collect();
        }

        // Get last 4 months for comparison
        $months = [];
        for ($i = 3; $i >= 0; $i--) {
            $date = Carbon::create($this->selectedYear, $this->selectedMonth)->subMonths($i);
            $months[] = [
                'month' => $date->month,
                'year' => $date->year,
                'label' => 'Tháng ' . $date->month,
                'is_current' => ($date->month == $this->selectedMonth && $date->year == $this->selectedYear),
            ];
        }

        return collect($months)->map(function ($item) {
            $report = DepartmentReport::where('department_id', $this->selectedDepartmentId)
                ->where('year', $item['year'])
                ->where('month', $item['month'])
                ->first();

            return array_merge($item, [
                'attendance' => $report?->total_attendance ?? 0,
                'max_attendance' => 1500, // For chart height calculation
            ]);
        });
    }

    public function render()
    {
        $departments = Department::orderBy('name')->get();
        $monthlyComparisons = $this->getMonthlyComparisons();

        return view('livewire.reports.department-report-dashboard', [
            'departments' => $departments,
            'monthlyComparisons' => $monthlyComparisons,
        ])->layout('layouts.app');
    }
}
