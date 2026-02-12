<?php

namespace App\Livewire;

use App\Config\FeatureConfig;
use App\Modules\Organization\Models\Department;
use Livewire\Component;

class DepartmentFeatureRegistry extends Component
{
    public $showSlideOver = false;
    public $department;
    public $departmentId;
    public $features = [];
    public $selectedFeatures = [];

    protected $listeners = ['openFeatureRegistry'];

    public function mount()
    {
        $this->features = FeatureConfig::getFeaturesByCategory();
    }

    public function openFeatureRegistry($departmentId)
    {
        $this->departmentId = $departmentId;
        $this->department = Department::find($departmentId);
        
        if ($this->department) {
            // Load existing settings
            $settings = $this->department->settings ?? [];
            $this->selectedFeatures = [];
            
            foreach (FeatureConfig::getFeatureKeys() as $key) {
                $this->selectedFeatures[$key] = isset($settings[$key]) && $settings[$key] === true;
            }
            
            $this->showSlideOver = true;
        }
    }

    public function toggleFeature($featureKey)
    {
        $this->selectedFeatures[$featureKey] = !($this->selectedFeatures[$featureKey] ?? false);
    }

    public function save()
    {
        if (!$this->department) {
            return;
        }

        // Prepare settings array
        $settings = $this->department->settings ?? [];
        
        foreach ($this->selectedFeatures as $key => $value) {
            $settings[$key] = $value;
        }

        // Save to department
        $this->department->settings = $settings;
        $this->department->save();

        // Close slide-over
        $this->showSlideOver = false;

        // Dispatch success event
        $this->dispatch('feature-registry-updated');
        
        // Show success message
        session()->flash('message', 'Đã cập nhật tính năng cho ' . $this->department->name);
    }

    public function cancel()
    {
        $this->showSlideOver = false;
        $this->reset(['departmentId', 'department', 'selectedFeatures']);
    }

    public function render()
    {
        return view('livewire.department-feature-registry');
    }
}
