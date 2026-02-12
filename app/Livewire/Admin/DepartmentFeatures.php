<?php

namespace App\Livewire\Admin;

use Livewire\Component;

use App\Modules\Organization\Models\Department;

class DepartmentFeatures extends Component
{
    public $departments;

    public function mount()
    {
        $this->refreshDepartments();
    }

    public function refreshDepartments()
    {
        $this->departments = Department::orderBy('name')->get();
    }

    public function toggleFeature($departmentId, $feature)
    {
        $department = Department::find($departmentId);
        if ($department) {
            if ($department->hasFeature($feature)) {
                $department->disableFeature($feature);
            } else {
                $department->enableFeature($feature);
            }
            $this->refreshDepartments(); // Refresh to reflect changes
            $this->dispatch('notify', 'Cập nhật thành công!');
        }
    }

    public function render()
    {
        return view('livewire.admin.department-features')->layout('layouts.app');
    }
}
