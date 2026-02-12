<?php

namespace App\Modules\Attendance\Livewire;

use Livewire\Component;

use App\Modules\Attendance\Models\AttendanceSession;
use App\Modules\Organization\Models\Department;
use Illuminate\Support\Facades\Auth;

class AttendanceDashboard extends Component
{
    public $sessions;
    
    // Context Switching
    public $manageableDepartments = [];
    public $selectedDepartmentId = null;

    // Slide-over Props
    public $showSlideOver = false;
    public $createMode = 'single'; // 'single' | 'bulk'
    
    // Single Create
    public $newSessionDate;
    public $newSessionType = 'sunday_service';
    public $newSessionName;

    // Bulk Create
    public $bulkStartDate;
    public $bulkEndDate;
    public $bulkDayOfWeek = 0; // 0 = Sunday, 1 = Monday, etc.

    // Manual Count Entry (Secretary only)
    public $manualCounts = [];

    public function mount()
    {
        // 1. Load Manageable Departments (Only 'Sinh hoạt')
        $query = Department::where('type', 'Sinh hoạt')->orderBy('name');

        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('admin')) {
            $deptIds = auth()->user()->getManageableDepartmentIds();
            if (!empty($deptIds)) {
                $query->whereIn('id', $deptIds);
            } else {
                // If user manages nothing, show nothing? or just view?
                // For now, let's allow viewing if they have permission, but list might be empty.
            }
        }
        
        $this->manageableDepartments = $query->get();

        // Auto-select if only 1
        if ($this->manageableDepartments->count() === 1) {
            $this->selectedDepartmentId = $this->manageableDepartments->first()->id;
        }

        $this->loadSessions();
        $this->newSessionDate = now()->format('Y-m-d');
        $this->bulkStartDate = now()->format('Y-m-d');
        $this->bulkEndDate = now()->addMonth()->format('Y-m-d');
    }

    public function selectDepartment($deptId)
    {
        $this->selectedDepartmentId = $deptId;
        $this->loadSessions();
    }

    public function loadSessions()
    {
        // ONLY Sunday Services
        $query = AttendanceSession::withSum('summaries', 'total_present')
            ->where('type', 'sunday_service')
            ->latest('date');

        // Logic: 
        // Sunday Services are usually Global (department_id = null).
        // If they are specific to a department (rare for Sunday Service in this context), we filter.
        // But usually "Hội thánh" means Global.
        
        // If a department is selected, maybe we want to see if that department has "checked in" for that service?
        // But the SESSION itself is the parent.
        // So we probably don't filter the session list by department_id unless the session belongs to a department.

        // Let's assume Sunday Services are Global.
        // So we show ALL sunday_services regardless of selected department.
        // The selected department might be used for "Entering Count" context later.
        
        // However, if the user created "Sunday Service" for "Youth Group" specifically?
        // Let's allow filtering if the session has a department_id.
        
        if ($this->selectedDepartmentId) {
             $query->where(function($q) {
                 $q->whereNull('department_id') // Global
                   ->orWhere('department_id', $this->selectedDepartmentId);
             });
        }

        $this->sessions = $query->take(20)->get();
        
        foreach ($this->sessions as $session) {
            $this->manualCounts[$session->id] = $session->manual_count;
        }
    }

    public function openSlideOver()
    {
        $this->resetValidation();
        $this->showSlideOver = true;
    }

    public function createSession()
    {
        if ($this->createMode === 'single') {
            $this->createSingleSession();
        } else {
            $this->createBulkSessions();
        }

        $this->showSlideOver = false;
        $this->loadSessions();
        session()->flash('success', 'Đã tạo buổi nhóm thành công.');
    }

    public function createSingleSession()
    {
        $this->validate([
            'newSessionDate' => 'required|date',
            'newSessionType' => 'required',
        ]);

        AttendanceSession::create([
            'date' => $this->newSessionDate,
            'type' => $this->newSessionType,
            'name' => $this->newSessionName,
            'status' => 'open',
        ]);
    }

    public function createBulkSessions()
    {
        $this->validate([
            'bulkStartDate' => 'required|date',
            'bulkEndDate' => 'required|date|after_or_equal:bulkStartDate',
            'newSessionType' => 'required',
            'bulkDayOfWeek' => 'required_if:createMode,bulk|integer|min:0|max:6',
        ]);

        $start = \Carbon\Carbon::parse($this->bulkStartDate);
        $end = \Carbon\Carbon::parse($this->bulkEndDate);

        // Determine target day of week
        // If type is sunday_service, force Sunday (0)
        // Otherwise use selected day
        $targetDay = ($this->newSessionType === 'sunday_service') ? 0 : (int) $this->bulkDayOfWeek;
        
        $current = $start->copy();
        $count = 0;

        while ($current <= $end) {
            if ($current->dayOfWeek === $targetDay) {
                AttendanceSession::firstOrCreate(
                    [
                        'date' => $current->format('Y-m-d'),
                        'type' => $this->newSessionType
                    ], 
                    [
                        // For non-Sunday types, don't default to "Thờ phượng Chúa nhật" name if possible
                        'name' => $this->newSessionName ?? ($this->newSessionType == 'sunday_service' ? 'Thờ phượng Chúa nhật' : 'Buổi nhóm'),
                        'status' => 'open'
                    ]
                );
                $count++;
            }
            $current->addDay();
        }

        if ($count == 0 && $this->newSessionType == 'sunday_service') {
            // Fallback or warning?
        }
    }

    public function updateManualCount($sessionId)
    {
        if (!Auth::user()->isSecretary()) {
            return;
        }

        $session = AttendanceSession::find($sessionId);
        if ($session && $session->status !== 'locked') {
            $session->update([
                'manual_count' => $this->manualCounts[$sessionId]
            ]);
            session()->flash('success', 'Đã cập nhật số đếm thực tế.');
        } else {
             session()->flash('error', 'Không thể sửa khi đã khóa sổ.');
        }
    }

    public function render()
    {
        return view('livewire.attendance-dashboard')->layout('layouts.app');
    }
}
