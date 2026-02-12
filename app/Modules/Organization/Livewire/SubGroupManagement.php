<?php

namespace App\Modules\Organization\Livewire;

use Livewire\Component;
use App\Modules\Organization\Models\Department;
use App\Modules\Organization\Models\SubGroup;
use App\Modules\Membership\Models\Member;

class SubGroupManagement extends Component
{
    public $departments;
    public $subGroups;
    public $selectedDepartmentId;
    
    // Modal properties
    public $isModalOpen = false;
    public $subGroupId;
    public $name;
    public $leaderId;
    public $confirmingDeletion = false;
    public $idToDelete;

    // Leader search
    public $leaderSearch = '';
    public $potentialLeaders = [];

    protected $rules = [
        'selectedDepartmentId' => 'required|exists:departments,id',
        'name' => 'required|string|max:255',
        'leaderId' => 'nullable|exists:members,id',
    ];

    public function mount()
    {
        $this->departments = Department::all();
        // Default to first department if exists
        if ($this->departments->isNotEmpty()) {
            $this->selectedDepartmentId = $this->departments->first()->id;
        }
        $this->loadSubGroups();
    }

    public function updatedSelectedDepartmentId()
    {
        $this->loadSubGroups();
    }

    public function loadSubGroups()
    {
        if ($this->selectedDepartmentId) {
            $this->subGroups = SubGroup::where('department_id', $this->selectedDepartmentId)
                ->with('leader')
                ->get();
        } else {
            $this->subGroups = collect();
        }
    }

    public function updatedLeaderSearch()
    {
        if (strlen($this->leaderSearch) > 1) {
            $this->potentialLeaders = Member::where('full_name', 'like', '%' . $this->leaderSearch . '%')
                ->take(5)
                ->get();
        } else {
            $this->potentialLeaders = [];
        }
    }

    public function selectLeader($id, $name)
    {
        $this->leaderId = $id;
        $this->leaderSearch = $name; // Display selected name
        $this->potentialLeaders = [];
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $subGroup = SubGroup::findOrFail($id);
        $this->subGroupId = $id;
        $this->name = $subGroup->name;
        $this->leaderId = $subGroup->leader_id;
        $this->leaderSearch = $subGroup->leader ? $subGroup->leader->full_name : '';
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        SubGroup::updateOrCreate(['id' => $this->subGroupId], [
            'department_id' => $this->selectedDepartmentId,
            'name' => $this->name,
            'leader_id' => $this->leaderId,
        ]);

        session()->flash('message', $this->subGroupId ? 'Cập nhật Tổ thành công.' : 'Thêm Tổ thành công.');
        $this->closeModal();
        $this->loadSubGroups();
    }

    public function delete($id)
    {
        $this->idToDelete = $id;
        $this->confirmingDeletion = true;
    }

    public function destroy()
    {
        if ($this->idToDelete) {
            SubGroup::find($this->idToDelete)->delete();
            session()->flash('message', 'Đã xóa Tổ.');
            $this->loadSubGroups();
        }
        $this->confirmingDeletion = false;
        $this->idToDelete = null;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->leaderId = null;
        $this->leaderSearch = '';
        $this->subGroupId = null;
        $this->potentialLeaders = [];
    }

    public function render()
    {
        return view('livewire.sub-group-management')->layout('layouts.app');
    }
}
