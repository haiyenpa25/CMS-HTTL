<?php

namespace App\Livewire;

use App\Helpers\MenuHelper;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserDashboard extends Component
{
    public $currentDepartment;
    public $availableFeatures = [];
    public $departments = [];
    public $quickActions = [];

    public function mount()
    {
        $user = Auth::user();
        
        // Get current department from session or first assigned department
        $this->currentDepartment = session('current_department_id');
        
        if (!$this->currentDepartment) {
            $firstDept = MenuHelper::getUserDepartments($user)->first();
            if ($firstDept) {
                $this->currentDepartment = $firstDept->id;
                session(['current_department_id' => $firstDept->id]);
            }
        }
        
        // Get available features
        $this->availableFeatures = MenuHelper::getAvailableFeatures($user);
        
        // Get user departments
        $this->departments = MenuHelper::getUserDepartments($user);
        
        // Build quick actions
        $this->quickActions = $this->buildQuickActions();
    }

    private function buildQuickActions()
    {
        $actions = [];
        
        if (in_array('attendance', $this->availableFeatures)) {
            $actions[] = [
                'title' => 'Điểm Danh Chủ Nhật',
                'description' => 'Điểm danh thành viên tham dự buổi nhóm',
                'icon' => 'check-circle',
                'route' => 'attendance.dashboard', // Existing route
                'color' => 'blue'
            ];
        }
        
        if (in_array('visits', $this->availableFeatures)) {
            $actions[] = [
                'title' => 'Thăm Viếng',
                'description' => 'Quản lý lịch thăm viếng',
                'icon' => 'calendar',
                'route' => 'visits.index', // Existing route
                'color' => 'purple'
            ];
        }
        
        // Only show actions for routes that exist
        // Other features can be added when routes are created
        
        return $actions;
    }

    public function switchDepartment($departmentId)
    {
        session(['current_department_id' => $departmentId]);
        $this->currentDepartment = $departmentId;
        $this->quickActions = $this->buildQuickActions();
        
        $this->dispatch('department-switched');
    }

    public function render()
    {
        return view('livewire.user-dashboard')->layout('layouts.app');
    }
}
