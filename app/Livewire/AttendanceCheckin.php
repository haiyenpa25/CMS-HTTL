<?php

namespace App\Livewire;

use Livewire\Component;
use App\Modules\Attendance\Models\AttendanceSession;
use App\Modules\Organization\Models\Department;
use App\Modules\Attendance\Models\Attendance;
use App\Modules\Attendance\Models\AttendanceSummary;
use App\Modules\Membership\Models\Member;
use Illuminate\Support\Facades\Auth;

class AttendanceCheckin extends Component
{
    public $session;
    public $departments;
    public $selectedDepartmentId;
    
    public $activeTab = 'detail'; // 'detail' or 'quick'
    
    // Quick Add Property
    public $quickAddCount = 0;

    // Listeners for real-time updates not strictly needed if wire:model works well, 
    // but for toggling boolean fields, direct methods are often snappier.

    public function getCurrentDepartmentProperty()
    {
        return $this->departments->firstWhere('id', $this->selectedDepartmentId);
    }

    public function mount($sessionId)
    {
        $this->session = AttendanceSession::findOrFail($sessionId);
        
        $manageableIds = Auth::user()->getManageableDepartmentIds();
        $this->departments = Department::whereIn('id', $manageableIds)->get();
        
        if ($this->departments->isNotEmpty()) {
            $this->selectedDepartmentId = $this->departments->first()->id;
            
            // Check access for the default selected
             \Illuminate\Support\Facades\Gate::authorize('manage-department', $this->selectedDepartmentId);
             
            $this->loadQuickAddData();
        } else {
            // Check if user has explicit assignment to a SubGroup but maybe confusing UI here
            // For now, if no department assignment, show empty or error?
            // User might be a super admin with no specific assignment but getManageable returns getAll.
            // If empty, it means they have no access.
        }
    }

    public function updatedSelectedDepartmentId()
    {
        if ($this->selectedDepartmentId) {
            \Illuminate\Support\Facades\Gate::authorize('manage-department', $this->selectedDepartmentId);
        }
        $this->loadQuickAddData();
    }

    public function loadQuickAddData()
    {
        if (!$this->selectedDepartmentId) return;

        $summary = AttendanceSummary::where('attendance_session_id', $this->session->id)
            ->where('department_id', $this->selectedDepartmentId)
            ->first();

        $this->quickAddCount = $summary ? $summary->total_present : 0;
    }

    public function getMembersProperty()
    {
        if (!$this->selectedDepartmentId) return collect();

        // Fetch members belonging to this department
        // Also Load their existing attendance for this session
        return Member::whereHas('departments', function($q) {
                $q->where('departments.id', $this->selectedDepartmentId);
            })
            ->with(['attendances' => function($q) {
                $q->where('attendance_session_id', $this->session->id);
            }])
            ->get();
    }

    public function togglePresence($memberId)
    {
        $this->updateAttendanceField($memberId, 'is_present');
    }

    public function toggleScripture($memberId)
    {
        $this->updateAttendanceField($memberId, 'memorized_scripture');
    }

    public function updateAnswers($memberId, $count)
    {
        $this->updateAttendance($memberId, ['bible_answers_count' => $count]);
    }

    protected function updateAttendanceField($memberId, $field)
    {
        if ($this->session->status === 'locked') {
            $this->dispatch('notify', 'Buổi điểm danh đã khóa!');
            return;
        }

        // Find existing or create new
        $attendance = Attendance::firstOrNew([
            'attendance_session_id' => $this->session->id,
            'member_id' => $memberId,
        ]);

        if (!$attendance->exists) {
            $attendance->department_id = $this->selectedDepartmentId; // Snapshot
            // Default other fields false
            $attendance->$field = true;
        } else {
            $attendance->$field = !$attendance->$field;
        }

        $attendance->save();
        
        // Update Summary automatically when Detail is used
        $this->recalculateSummary();
    }
    
    protected function updateAttendance($memberId, $data)
    {
        $attendance = Attendance::updateOrCreate(
            [
                'attendance_session_id' => $this->session->id,
                'member_id' => $memberId,
            ],
            array_merge($data, ['department_id' => $this->selectedDepartmentId])
        );
        $this->recalculateSummary();
    }

    protected function recalculateSummary()
    {
        // Count total present for this session + dept
        $count = Attendance::where('attendance_session_id', $this->session->id)
            ->where('department_id', $this->selectedDepartmentId)
            ->where('is_present', true)
            ->count();
            
        AttendanceSummary::updateOrCreate(
            [
                'attendance_session_id' => $this->session->id,
                'department_id' => $this->selectedDepartmentId,
            ],
            [
                'total_present' => $count,
                'is_manual_entry' => false
            ]
        );
        
        $this->quickAddCount = $count;
    }

    public function saveQuickAdd()
    {
        if ($this->session->status === 'locked') {
            session()->flash('error', 'Buổi điểm danh đã khóa!');
            return;
        }

        $this->validate([
            'quickAddCount' => 'required|integer|min:0'
        ]);

        AttendanceSummary::updateOrCreate(
            [
                'attendance_session_id' => $this->session->id,
                'department_id' => $this->selectedDepartmentId,
            ],
            [
                'total_present' => $this->quickAddCount,
                'is_manual_entry' => true
            ]
        );

        session()->flash('message', 'Đã lưu số liệu tổng.');
    }

    public function lockSession()
    {
        if (!Auth::user()->isSecretary()) {
            return;
        }

        $this->session->update(['status' => 'locked']);
        session()->flash('message', 'Đã khóa sổ buổi điểm danh này. Không thể chỉnh sửa.');
    }

    public function render()
    {
        return view('livewire.attendance-checkin')->layout('layouts.app');
    }
}
