<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Modules\Membership\Models\Member;
use App\Modules\Organization\Models\Department;
use App\Modules\Organization\Models\SubGroup;
use Illuminate\Support\Facades\DB;

class MemberAssignment extends Component
{
    use WithPagination;

    // Filter/Search Source
    public $sourceSearch = '';
    public $selectedMembers = []; // Array of member IDs
    
    // Target Selection
    public $departments;
    public $subGroups;
    public $selectedDepartmentId;
    public $selectedSubGroupId;
    
    // Target List (Members in selected Dept/SubGroup)
    public $targetMembers;

    protected $listeners = ['refreshTarget' => 'loadTargetMembers'];

    public function mount()
    {
        $this->departments = Department::all();
        if ($this->departments->isNotEmpty()) {
            $this->selectedDepartmentId = $this->departments->first()->id;
        }
        $this->loadSubGroups();
        $this->loadTargetMembers();
    }

    public function updatedSourceSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedDepartmentId()
    {
        $this->selectedSubGroupId = null;
        $this->loadSubGroups();
        $this->loadTargetMembers();
    }

    public function updatedSelectedSubGroupId()
    {
        $this->loadTargetMembers();
    }

    public function loadSubGroups()
    {
        if ($this->selectedDepartmentId) {
            $this->subGroups = SubGroup::where('department_id', $this->selectedDepartmentId)->get();
        } else {
            $this->subGroups = collect();
        }
    }

    public function loadTargetMembers()
    {
        if (!$this->selectedDepartmentId) {
            $this->targetMembers = collect();
            return;
        }

        // Fetch members assigned to this department
        // Pivot table: department_member
        $query = DB::table('department_member')
            ->join('members', 'department_member.member_id', '=', 'members.id')
            ->where('department_member.department_id', $this->selectedDepartmentId)
            ->select(
                'members.id as member_id',
                'members.full_name',
                'department_member.id as pivot_id',
                'department_member.role',
                'department_member.sub_group_id'
            );

        if ($this->selectedSubGroupId) {
            $query->where('department_member.sub_group_id', $this->selectedSubGroupId);
        }

        $this->targetMembers = $query->get();
    }

    public function toggleMemberSelection($memberId)
    {
        if (in_array($memberId, $this->selectedMembers)) {
            $this->selectedMembers = array_diff($this->selectedMembers, [$memberId]);
        } else {
            $this->selectedMembers[] = $memberId;
        }
    }

    public function assignSelected()
    {
        if (empty($this->selectedMembers) || !$this->selectedDepartmentId) {
            return;
        }

        foreach ($this->selectedMembers as $memberId) {
            // Check if already exists to prevent duplicate error (though DB has unique constraint)
            // Or use updateOrInsert
            
            DB::table('department_member')->updateOrInsert(
                [
                    'department_id' => $this->selectedDepartmentId,
                    'member_id' => $memberId,
                ],
                [
                    'sub_group_id' => $this->selectedSubGroupId, // Can be null
                    'role' => 'member',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->selectedMembers = [];
        $this->loadTargetMembers();
        session()->flash('message', 'Đã gán thành viên thành công.');
    }

    public function removeFromDept($pivotId)
    {
        DB::table('department_member')->where('id', $pivotId)->delete();
        $this->loadTargetMembers();
    }

    public function updateRole($pivotId, $newRole)
    {
        DB::table('department_member')->where('id', $pivotId)->update(['role' => $newRole]);
        $this->loadTargetMembers();
    }
    
    public function updateSubGroup($pivotId, $subGroupId)
    {
        // subGroupId might be '' string from select
        $val = $subGroupId ?: null;
        DB::table('department_member')->where('id', $pivotId)->update(['sub_group_id' => $val]);
        $this->loadTargetMembers();
    }

    public function render()
    {
        $sourceMembersQuery = Member::query();

        if ($this->sourceSearch) {
            $sourceMembersQuery->where('full_name', 'like', '%' . $this->sourceSearch . '%');
        }
        
        // Exclude members already in the selected View (Target) to avoid confusion?
        // Or keep them but mark as assigned?
        // User pattern: "Checkbox row". If member is already in target, maybe disable checkbox or show status.
        // For performance, let's just paginate all.
        
        $sourceMembers = $sourceMembersQuery->paginate(10);

        return view('livewire.member-assignment', [
            'sourceMembers' => $sourceMembers
        ])->layout('layouts.app');
    }
}
