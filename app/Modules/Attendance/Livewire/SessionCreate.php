<?php

namespace App\Modules\Attendance\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Modules\Attendance\Models\AttendanceSession;
use App\Modules\Membership\Models\Department;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.app')]
class SessionCreate extends Component
{
    // Mode: single or bulk
    public $createMode = 'single';
    
    // Common Fields
    public $newSessionType = 'sunday_service';
    public $newSessionName = '';
    
    // Single Mode
    public $newSessionDate;
    
    // Bulk Mode
    public $bulkStartDate;
    public $bulkEndDate;
    public $bulkDayOfWeek = 0; // 0 = Sunday
    
    // Departments (for context)
    public $departments;
    public $department_id; // Selected department context (if creating for specific dep)

    protected function rules()
    {
        return [
            'newSessionType' => 'required',
            'newSessionName' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            
            // Conditional rules
            'newSessionDate' => 'required_if:createMode,single|date',
            
            'bulkStartDate' => 'required_if:createMode,bulk|date',
            'bulkEndDate' => 'required_if:createMode,bulk|date|after_or_equal:bulkStartDate',
            'bulkDayOfWeek' => 'required_if:createMode,bulk|integer|min:0|max:6',
        ];
    }

    public function mount()
    {
        $this->departments = Department::orderBy('name')->get();
        $this->newSessionDate = now()->format('Y-m-d');
        $this->bulkStartDate = now()->format('Y-m-d');
        $this->bulkEndDate = now()->addMonths(1)->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.attendance.session-create');
    }

    public function createSession()
    {
        $this->validate();

        if ($this->createMode === 'single') {
            $this->createSingleSession();
        } else {
            $this->createBulkSessions();
        }
        
        // Reset or Redirect
        session()->flash('success', 'Đã tạo buổi nhóm thành công.');
        
        // Option 1: Stay and reset
        // $this->reset(['newSessionName', 'newSessionDate']);
        
        // Option 2: Redirect to List
        return redirect()->route('sessions.index'); 
    }

    public function createSingleSession()
    {
        AttendanceSession::create([
            'date' => $this->newSessionDate,
            'type' => $this->newSessionType,
            'name' => $this->newSessionName ?: $this->getDefaultName(),
            'status' => 'open',
            'department_id' => $this->department_id,
        ]);
    }

    public function createBulkSessions()
    {
        $start = Carbon::parse($this->bulkStartDate);
        $end = Carbon::parse($this->bulkEndDate);

        // Determine target day of week
        $targetDay = ($this->newSessionType === 'sunday_service') ? 0 : (int) $this->bulkDayOfWeek;
        
        $current = $start->copy();
        $count = 0;

        DB::transaction(function () use ($current, $end, $targetDay, &$count) {
            while ($current <= $end) {
                if ($current->dayOfWeek === $targetDay) {
                    AttendanceSession::firstOrCreate(
                        [
                            'date' => $current->format('Y-m-d'),
                            'type' => $this->newSessionType,
                            'department_id' => $this->department_id, // Scope by department if set
                            // Note: If department_id is null (Global), make sure we check that too.
                            // However, firstOrCreate uses the attributes in first array to check existence.
                            // If department_id is in fillable, we should include it here to avoid duplicates for same date/type/dept.
                        ], 
                        [
                            'name' => $this->newSessionName ?: $this->getDefaultName(),
                            'status' => 'open'
                        ]
                    );
                    $count++;
                }
                $current->addDay();
            }
        });
        
        if ($count > 0) {
            session()->flash('success', "Đã tạo $count buổi nhóm thành công.");
        } else {
            session()->flash('warning', "Không có ngày nào phù hợp trong khoảng thời gian đã chọn.");
        }
    }
    
    private function getDefaultName()
    {
        return $this->newSessionType == 'sunday_service' ? 'Thờ phượng Chúa nhật' : 'Buổi nhóm';
    }
}
