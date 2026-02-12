<?php

namespace App\Modules\Identity\Livewire;

use App\Config\FeatureConfig;
use App\Modules\Organization\Models\Department;
use App\Modules\Identity\Models\User;
use App\Modules\Identity\Models\UserAssignment;
use Livewire\Component;

class UserPermissionAssignment extends Component
{
    public $showModal = false;
    public $currentStep = 1;
    public $user;
    public $userId;
    
    // Step 1: Department Selection
    public $selectedDepartmentId;
    public $availableDepartments = [];
    
    // Step 2: Feature Assignment
    public $availableFeatures = [];
    public $selectedFeatures = [];
    
    // Existing assignment (for edit mode)
    public $existingAssignment;

    protected $listeners = ['openPermissionAssignment'];

    public function openPermissionAssignment($userId)
    {
        $this->userId = $userId;
        $this->user = User::find($userId);
        
        if ($this->user) {
            $this->reset(['currentStep', 'selectedDepartmentId', 'selectedFeatures', 'existingAssignment']);
            $this->currentStep = 1;
            $this->loadAvailableDepartments();
            $this->showModal = true;
        }
    }

    public function loadAvailableDepartments()
    {
        $this->availableDepartments = Department::where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function selectDepartment()
    {
        $this->validate([
            'selectedDepartmentId' => 'required|exists:departments,id'
        ]);

        // Load department features
        $department = Department::find($this->selectedDepartmentId);
        $departmentSettings = $department->settings ?? [];
        
        // Filter features: only show features enabled for this department
        $allFeatures = FeatureConfig::getAllFeatures();
        $this->availableFeatures = array_filter($allFeatures, function($feature) use ($departmentSettings) {
            return isset($departmentSettings[$feature['key']]) && $departmentSettings[$feature['key']] === true;
        });

        // Check if user already has assignment for this department
        $this->existingAssignment = UserAssignment::where('user_id', $this->userId)
            ->where('department_id', $this->selectedDepartmentId)
            ->first();

        // Load existing permissions if any
        if ($this->existingAssignment && $this->existingAssignment->allowed_features) {
            $this->selectedFeatures = $this->existingAssignment->allowed_features;
        } else {
            $this->selectedFeatures = [];
        }

        // Move to step 2
        $this->currentStep = 2;
    }

    public function toggleFeature($featureKey)
    {
        if (in_array($featureKey, $this->selectedFeatures)) {
            $this->selectedFeatures = array_values(array_diff($this->selectedFeatures, [$featureKey]));
        } else {
            $this->selectedFeatures[] = $featureKey;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function save()
    {
        if (empty($this->selectedFeatures)) {
            session()->flash('error', 'Vui lòng chọn ít nhất một tính năng');
            return;
        }

        // Create or update assignment
        if ($this->existingAssignment) {
            $this->existingAssignment->allowed_features = $this->selectedFeatures;
            $this->existingAssignment->save();
        } else {
            UserAssignment::create([
                'user_id' => $this->userId,
                'department_id' => $this->selectedDepartmentId,
                'allowed_features' => $this->selectedFeatures,
            ]);
        }

        // Close modal
        $this->showModal = false;

        // Dispatch success event
        $this->dispatch('permission-assignment-updated');
        
        // Show success message
        session()->flash('message', 'Đã cập nhật quyền cho ' . $this->user->name);
    }

    public function cancel()
    {
        $this->showModal = false;
        $this->reset(['userId', 'user', 'currentStep', 'selectedDepartmentId', 'selectedFeatures', 'existingAssignment']);
    }

    public function render()
    {
        return view('livewire.user-permission-assignment');
    }
}
