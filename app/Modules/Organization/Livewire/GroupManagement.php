<?php

namespace App\Modules\Organization\Livewire;

use App\Modules\Organization\Models\Department;
use App\Modules\Membership\Models\Member;
use App\Modules\Organization\Models\SubGroup;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GroupManagement extends Component
{
    public $editingGroup;
    public $showManageModal = false;
    public $showCreateModal = false;

    // Feature Toggles state
    public $availableFeatures = ['attendance', 'scheduling', 'inventory'];

    // Sub-group state
    public $newSubGroupName = '';
    public $newSubGroupLeaderId = null;

    // Create Group state
    public $newGroupName = '';
    public $newGroupType = 'Sinh hoạt';
    public $newGroupDescription = '';

    protected $rules = [
        'editingGroup.name' => 'required|string|max:255',
        'editingGroup.type' => 'required|in:Lãnh đạo,Sinh hoạt,Mục vụ',
        'editingGroup.description' => 'nullable|string',
    ];

    public function getGroupsProperty()
    {
        return Department::with(['subGroups.leader', 'members'])->get(); // Eager load relationships
    }

    public function getMembersProperty()
    {
        return Member::orderBy('full_name')->get();
    }

    public function editGroup($groupId)
    {
        $this->editingGroup = Department::with('subGroups')->find($groupId);
        $this->showManageModal = true;
    }

    public function updatedEditingGroup($value, $key)
    {
        // Auto-save specific fields if needed, or just allow manual save
        // For now, we will save on 'saveGroup' or similar
    }

    public function saveGroup()
    {
        $this->validate();
        $this->editingGroup->save();
        $this->dispatch('notify', 'Đã lưu thay đổi thông tin Ban/Ngành.');
    }

    public function createGroup()
    {
        $this->validate([
            'newGroupName' => 'required|string|max:255',
            'newGroupType' => 'required|in:Lãnh đạo,Sinh hoạt,Mục vụ',
        ]);

        Department::create([
            'name' => $this->newGroupName,
            'type' => $this->newGroupType,
            'description' => $this->newGroupDescription,
        ]);

        $this->reset(['newGroupName', 'newGroupType', 'newGroupDescription', 'showCreateModal']);
        $this->dispatch('notify', 'Đã tạo Ban/Ngành mới.');
    }

    public function toggleFeature($feature)
    {
        if (!$this->editingGroup) return;

        if ($this->editingGroup->hasFeature($feature)) {
            $this->editingGroup->disableFeature($feature);
        } else {
            $this->editingGroup->enableFeature($feature);
        }
        // Force refresh of the model attribute
        $this->editingGroup->refresh(); 
    }

    public function addSubGroup()
    {
        $this->validate([
            'newSubGroupName' => 'required|string|max:255',
        ]);

        $this->editingGroup->subGroups()->create([
            'name' => $this->newSubGroupName,
            'leader_id' => $this->newSubGroupLeaderId,
        ]);

        $this->reset(['newSubGroupName', 'newSubGroupLeaderId']);
        $this->editingGroup->refresh(); // Refresh to show new sub-group
        $this->dispatch('notify', 'Đã thêm Tổ mới.');
    }

    public function deleteSubGroup($subGroupId)
    {
        SubGroup::destroy($subGroupId);
        $this->editingGroup->refresh();
        $this->dispatch('notify', 'Đã xóa Tổ.');
    }

    public function deleteGroup($groupId)
    {
        Department::destroy($groupId);
        $this->dispatch('notify', 'Đã xóa Ban/Ngành.');
        $this->showManageModal = false;
    }

    public function render()
    {
        return view('livewire.group-management', [
            'groups' => $this->groups,
            'members' => $this->members,
        ]);
    }
}
