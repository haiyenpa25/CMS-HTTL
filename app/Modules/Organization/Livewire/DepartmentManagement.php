<?php

namespace App\Modules\Organization\Livewire;

use Livewire\Component;
use App\Modules\Organization\Models\Department;
use Illuminate\Validation\Rule;

class DepartmentManagement extends Component
{
    public $departments;
    public $name;
    public $description;
    public $type;
    public $features = [];
    public $status = 'active';
    public $departmentId;
    public $isModalOpen = false;
    public $confirmingDeptDeletion = false;
    public $departmentIdToDelete;
    public $search = '';

    // Define available features for the toggles
    const AVAILABLE_FEATURES = [
        'attendance' => 'Điểm danh',
        'scheduling' => 'Sắp lịch',
        'inventory' => 'Kho',
        'reports' => 'Báo cáo',
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['Lãnh đạo', 'Sinh hoạt', 'Mục vụ'])],
            'features' => 'array',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function mount()
    {
        $this->isModalOpen = false;
        $this->departmentId = null;
        $this->features = [];
    }

    public function render()
    {
        $query = Department::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        $this->departments = $query->get();

        return view('livewire.department-management', [
            'availableFeatures' => self::AVAILABLE_FEATURES,
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->type = 'Sinh hoạt'; // Default
        $this->features = [];
        $this->status = 'active';
        $this->departmentId = null;
    }

    public function store()
    {
        $this->validate();

        // Convert array features to boolean map based on available keys if needed, 
        // OR just save the array of checked items.
        // The Model casts 'features' to array.
        // Let's ensure structure: ['feature_key' => true/false] or just keys present means true.
        // For simplicity and model helper compatibility: ['attendance' => true]
        
        $featuresToSave = [];
        foreach (self::AVAILABLE_FEATURES as $key => $label) {
            if (in_array($key, $this->features)) {
                $featuresToSave[$key] = true;
            }
        }

        Department::updateOrCreate(['id' => $this->departmentId], [
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'features' => $featuresToSave,
            'status' => $this->status,
        ]);

        session()->flash('message', $this->departmentId ? 'Cập nhật thành công.' : 'Tạo mới thành công.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $this->departmentId = $id;
        $this->name = $department->name;
        $this->description = $department->description;
        $this->type = $department->type;
        $this->status = $department->status;
        
        // Transform JSON features back to array for checkboxes
        // If Model returns ['attendance' => true], we want $this->features to include 'attendance'
        $this->features = [];
        if ($department->features) {
            foreach ($department->features as $key => $enabled) {
                if ($enabled) {
                    $this->features[] = $key;
                }
            }
        }

        $this->openModal();
    }

    public function delete($id)
    {
        $this->departmentIdToDelete = $id;
        $this->confirmingDeptDeletion = true;
    }

    public function destroy()
    {
        if ($this->departmentIdToDelete) {
            $department = Department::find($this->departmentIdToDelete);
            if ($department) {
                $department->delete();
                session()->flash('message', 'Đã xóa ban ngành.');
            }
        }
        $this->confirmingDeptDeletion = false;
        $this->departmentIdToDelete = null;
    }
}
