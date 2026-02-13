<?php

namespace App\Modules\Attendance\Livewire;

use App\Modules\Attendance\Models\{AttendanceSession, SessionAssignment};
use App\Modules\Attendance\Enums\MinistryRole;
use App\Modules\Membership\Models\{Department, Member};
use App\Modules\Speakers\Models\Speaker;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class SessionManager extends Component
{
    public $sessions;
    public $speakers;
    public $members;
    public $departments;
    
    // Form fields - Session Info
    public $sessionId;
    public $date;
    public $name;
    public $department_id;
    public $topic;
    public $main_scripture;
    public $key_verse;
    public $speaker_id;
    public $mc_id;
    public $notes;
    public $type = 'sunday_service';
    public $status = 'open';
    
    // Assignment management
    public $assignments = [];
    public $newAssignment = ['member_id' => '', 'role_name' => '', 'note' => ''];
    
    // UI state
    public $isModalOpen = false;
    public $confirmingDeletion = false;
    public $idToDelete;
    public $search = '';
    public $filterDepartment = '';
    public $activeTab = 'info'; // info, assignments
    public $selectedMonth; // Y-m

    public function getAvailableMonthsProperty()
    {
        $months = [];
        $currentDate = now()->addMonths(6); // Future 6 months
        $endDate = now()->subYear(); // Past 1 year

        while ($currentDate >= $endDate) {
            $value = $currentDate->format('Y-m');
            $label = "Tháng " . $currentDate->format('m/Y');
            if ($currentDate->isCurrentMonth()) {
                $label .= " (Hiện tại)";
            }
            $months[$value] = $label;
            $currentDate->subMonth();
        }
        return $months;
    }

    protected function rules()
    {
        return [
            'date' => 'required|date',
            'name' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'topic' => 'nullable|string|max:255',
            'main_scripture' => 'nullable|string|max:255',
            'key_verse' => 'nullable|string|max:255',
            'speaker_id' => 'nullable|exists:speakers,id',
            'mc_id' => 'nullable|exists:members,id',
            'notes' => 'nullable|string',
        ];
    }

    public function mount()
    {
        $this->selectedMonth = now()->format('Y-m');
        $this->loadData();
        $this->speakers = Speaker::orderBy('name')->get();
        // Initial load - load all or empty? Let's load all for now to be safe, 
        // or effectively we should trigger loadMembers based on defaults.
        $this->members = Member::orderBy('full_name')->get();
        $this->departments = Department::where('type', 'Sinh hoạt')->orderBy('name')->get();
        $this->date = now()->format('Y-m-d');
    }

    public function updatedDepartmentId()
    {
        $this->loadMembers();
    }

    public function loadMembers()
    {
        if ($this->department_id) {
            $this->members = Member::whereHas('departments', function ($q) {
                $q->where('departments.id', $this->department_id);
            })->orderBy('full_name')->get();
        } else {
            // Optional: Filter by 'Sinh hoạt' only if no specific dept? 
            // Or just all members?
            $this->members = Member::orderBy('full_name')->get();
        }
    }

    public function loadData()
    {
        // ... existing loadData code ...
        $query = AttendanceSession::with(['department', 'speaker', 'mc', 'assignments.member']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('topic', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterDepartment) {
            $query->where('department_id', $this->filterDepartment);
        }

        if ($this->selectedMonth) {
            $year = substr($this->selectedMonth, 0, 4);
            $month = substr($this->selectedMonth, 5, 2);
            $query->whereYear('date', $year)
                  ->whereMonth('date', $month);
        }

        $this->sessions = $query->orderBy('date', 'desc')->get();
    }
    
    public function render()
    {
        $rolesByCategory = MinistryRole::getRolesByCategory();
        
        return view('livewire.attendance.session-manager', [
            'rolesByCategory' => $rolesByCategory,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function edit($id)
    {
        $session = AttendanceSession::with('assignments')->findOrFail($id);
        
        $this->sessionId = $id;
        $this->date = $session->date->format('Y-m-d');
        $this->name = $session->name;
        $this->department_id = $session->department_id;
        
        // Trigger member load based on the session's department
        $this->loadMembers();

        $this->topic = $session->topic;
        $this->main_scripture = $session->main_scripture;
        $this->key_verse = $session->key_verse;
        $this->speaker_id = $session->speaker_id;
        $this->mc_id = $session->mc_id;
        $this->notes = $session->notes;
        $this->type = $session->type ?? 'sunday_service';
        $this->status = $session->status ?? 'open';
        
        // Load assignments
        $this->assignments = $session->assignments->map(function($assignment) {
            return [
                'member_id' => $assignment->member_id,
                'role_name' => $assignment->role_name,
                'note' => $assignment->note,
            ];
        })->toArray();
        
        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        DB::transaction(function () {
            $session = AttendanceSession::updateOrCreate(
                ['id' => $this->sessionId],
                [
                    'date' => $this->date,
                    'name' => $this->name,
                    'department_id' => $this->department_id,
                    'topic' => $this->topic,
                    'main_scripture' => $this->main_scripture,
                    'key_verse' => $this->key_verse,
                    'speaker_id' => $this->speaker_id,
                    'mc_id' => $this->mc_id,
                    'notes' => $this->notes,
                    'type' => $this->type,
                    'status' => $this->status,
                ]
            );

            // Sync assignments
            $session->assignments()->delete();
            foreach ($this->assignments as $assignment) {
                if ($assignment['member_id'] && $assignment['role_name']) {
                    $session->assignments()->create($assignment);
                }
            }
        });

        session()->flash('message', $this->sessionId ? 'Cập nhật buổi nhóm thành công.' : 'Thêm buổi nhóm thành công.');
        $this->closeModal();
        $this->loadData();
    }

    public function addAssignment()
    {
        if ($this->newAssignment['member_id'] && $this->newAssignment['role_name']) {
            $this->assignments[] = $this->newAssignment;
            $this->newAssignment = ['member_id' => '', 'role_name' => '', 'note' => ''];
        }
    }

    public function removeAssignment($index)
    {
        unset($this->assignments[$index]);
        $this->assignments = array_values($this->assignments);
    }

    public function delete($id)
    {
        $this->idToDelete = $id;
        $this->confirmingDeletion = true;
    }

    public function destroy()
    {
        if ($this->idToDelete) {
            AttendanceSession::find($this->idToDelete)->delete();
            session()->flash('message', 'Đã xóa buổi nhóm.');
            $this->loadData();
        }
        $this->confirmingDeletion = false;
        $this->idToDelete = null;
    }

    public function openModal()
    {
        $this->isModalOpen = true;
        $this->activeTab = 'info';
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }
    
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    private function resetInputFields()
    {
        $this->sessionId = null;
        $this->date = now()->format('Y-m-d');
        $this->name = '';
        $this->department_id = '';
        $this->topic = '';
        $this->main_scripture = '';
        $this->key_verse = '';
        $this->speaker_id = '';
        $this->mc_id = '';
        $this->notes = '';
        $this->type = 'sunday_service';
        $this->status = 'open';
        $this->assignments = [];
        $this->newAssignment = ['member_id' => '', 'role_name' => '', 'note' => ''];
        $this->activeTab = 'info';
    }

    public function updatedSearch()
    {
        $this->loadData();
    }

    public function updatedFilterDepartment()
    {
        $this->loadData();
    }

    public function updatedSelectedMonth()
    {
        $this->loadData();
    }
}
