<?php

namespace App\Livewire;

use App\Modules\Attendance\Models\Attendance;
use App\Modules\Attendance\Models\AttendanceSession;
use App\Modules\Organization\Models\Department;
use App\Modules\Membership\Models\Member;
use App\Modules\Organization\Models\SubGroup;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DepartmentAttendance extends Component
{
    public $selectedSessionId;
    public $selectedDepartmentId;
    public $selectedSubGroupId;
    
    public $sessions = [];
    public $departments = [];
    public $subGroups = [];
    public $members = [];
    public $attendanceRecords = [];
    public $departmentFeatures = [];

    public function mount()
    {
        // Check permission
        $hasPermission = Auth::user()->assignments()
            ->whereJsonContains('allowed_features', 'group_attendance')
            ->exists();
        
        if (!$hasPermission && !Auth::user()->hasRole('admin') && !Auth::user()->hasRole('super-admin')) {
            abort(403, 'Bạn không có quyền truy cập tính năng này');
        }

        $this->loadUserDepartments();
        $this->loadUserDepartments();
    }

    public function loadUserDepartments()
    {
        // 1. Base Query: Only 'Sinh hoạt' departments
        $query = Department::where('type', 'Sinh hoạt')->where('status', 'active')->orderBy('name');

        // Super-admin/Admin: see all departments
        if (Auth::user()->hasRole('super-admin') || Auth::user()->hasRole('admin')) {
            // No extra filter
        } else {
            // Staff: only assigned departments with group_attendance permission
            $assignmentIds = Auth::user()->assignments()
                ->whereJsonContains('allowed_features', 'group_attendance')
                ->pluck('department_id')
                ->filter();
            
            $query->whereIn('id', $assignmentIds);
        }
        
        $this->departments = $query->get();
    }

    // Removed loadSessions() from mount as it is now dependent on Department selection

    public function updatedSelectedDepartmentId($value)
    {
        $this->selectedSessionId = null;
        $this->selectedSubGroupId = null;
        $this->sessions = [];
        $this->subGroups = [];
        $this->members = [];
        $this->attendanceRecords = [];

        if ($value) {
            $this->subGroups = SubGroup::where('department_id', $value)
                ->orderBy('name')
                ->get();
            
            // Load Sessions for this Department
            $this->sessions = AttendanceSession::where('department_id', $value)
                ->whereIn('type', ['department_meeting', 'active_group'])
                ->orderBy('date', 'desc')
                ->take(30)
                ->get();
            
            // Load department features
            $dept = Department::find($value);
            $settings = $dept->settings ?? [];
            
            // Always enable scripture tracking (default true)
            $this->departmentFeatures = [
                'scripture_tracking' => $settings['scripture_tracking'] ?? true
            ];
        }
    }

    public function updatedSelectedSubGroupId($value)
    {
        $this->members = [];
        $this->attendanceRecords = [];

        if ($value && $this->selectedSessionId) {
            $this->loadMembersAndAttendance();
        }
    }

    public function updatedSelectedSessionId($value)
    {
        if ($value && $this->selectedSubGroupId) {
            $this->loadMembersAndAttendance();
        }
    }

    public function loadMembersAndAttendance()
    {
        if (!$this->selectedSubGroupId || !$this->selectedSessionId) {
            return;
        }

        // Get members assigned to this department
        // Note: We use department_member pivot, not sub_group_member
        $this->members = Member::whereHas('departments', function($q) {
            $q->where('departments.id', $this->selectedDepartmentId);
        })->orderBy('full_name')->get();

        // Load existing attendance records for this sub-group
        $records = Attendance::where('attendance_session_id', $this->selectedSessionId)
            ->where('sub_group_id', $this->selectedSubGroupId)
            ->get()
            ->keyBy('member_id');

        // Initialize attendance records array
        $this->attendanceRecords = [];
        foreach ($this->members as $member) {
            if (isset($records[$member->id])) {
                $this->attendanceRecords[$member->id] = [
                    'id' => $records[$member->id]->id,
                    'is_present' => $records[$member->id]->is_present,
                    'memorized_scripture' => $records[$member->id]->memorized_scripture,
                    'bible_answers_count' => $records[$member->id]->bible_answers_count,
                ];
            } else {
                $this->attendanceRecords[$member->id] = [
                    'id' => null,
                    'is_present' => false,
                    'memorized_scripture' => false,
                    'bible_answers_count' => 0,
                ];
            }
        }
    }

    public function togglePresence($memberId)
    {
        $record = $this->getOrCreateAttendanceRecord($memberId);
        $record->is_present = !$record->is_present;
        $record->save();

        $this->attendanceRecords[$memberId]['is_present'] = $record->is_present;
        $this->attendanceRecords[$memberId]['id'] = $record->id;
    }

    public function toggleScripture($memberId)
    {
        $record = $this->getOrCreateAttendanceRecord($memberId);
        $record->memorized_scripture = !$record->memorized_scripture;
        $record->save();

        $this->attendanceRecords[$memberId]['memorized_scripture'] = $record->memorized_scripture;
        $this->attendanceRecords[$memberId]['id'] = $record->id;
    }

    public function incrementQuiz($memberId)
    {
        $record = $this->getOrCreateAttendanceRecord($memberId);
        $record->bible_answers_count = ($record->bible_answers_count ?? 0) + 1;
        $record->save();

        $this->attendanceRecords[$memberId]['bible_answers_count'] = $record->bible_answers_count;
        $this->attendanceRecords[$memberId]['id'] = $record->id;
    }

    public function decrementQuiz($memberId)
    {
        $record = $this->getOrCreateAttendanceRecord($memberId);
        $record->bible_answers_count = max(0, ($record->bible_answers_count ?? 0) - 1);
        $record->save();

        $this->attendanceRecords[$memberId]['bible_answers_count'] = $record->bible_answers_count;
        $this->attendanceRecords[$memberId]['id'] = $record->id;
    }

    public function updateQuizScore($memberId, $score)
    {
        $record = $this->getOrCreateAttendanceRecord($memberId);
        $record->bible_answers_count = max(0, (int)$score);
        $record->save();

        $this->attendanceRecords[$memberId]['bible_answers_count'] = $record->bible_answers_count;
        $this->attendanceRecords[$memberId]['id'] = $record->id;
    }

    private function getOrCreateAttendanceRecord($memberId)
    {
        if (isset($this->attendanceRecords[$memberId]['id']) && $this->attendanceRecords[$memberId]['id']) {
            return Attendance::find($this->attendanceRecords[$memberId]['id']);
        }

        // Use firstOrCreate to avoid unique constraint violation
        $record = Attendance::firstOrCreate(
            [
                'attendance_session_id' => $this->selectedSessionId,
                'member_id' => $memberId,
            ],
            [
                'department_id' => $this->selectedDepartmentId,
                'sub_group_id' => $this->selectedSubGroupId,
                'is_present' => false,
                'memorized_scripture' => false,
                'bible_answers_count' => 0,
            ]
        );

        return $record;
    }

    public function render()
    {
        return view('livewire.department-attendance')->layout('layouts.app');
    }
}
