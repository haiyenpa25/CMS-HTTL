<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Livewire components from modules
        \Livewire\Livewire::component('asset-maintenance-center', \App\Modules\Assets\Livewire\AssetMaintenanceCenter::class);
        \Livewire\Livewire::component('asset-procurement', \App\Modules\Assets\Livewire\AssetProcurement::class);
        \Livewire\Livewire::component('asset-management', \App\Modules\Assets\Livewire\AssetManagement::class);
        \Livewire\Livewire::component('asset-dashboard', \App\Modules\Assets\Livewire\AssetDashboard::class);
        
        \Illuminate\Support\Facades\Gate::define('manage-department', function ($user, $departmentId) {
            // 1. Super Admin/Admin/Secretary can manage ALL
            if ($user->isSecretary()) {
                return true;
            }

            // 2. Check if user is assigned to this department
            // We check if they have a direct assignment to the department
            return $user->assignments()
                ->where('department_id', $departmentId)
                ->exists();
                
            // Note: If they are assigned to a SUBGROUP, they might not manage the whole department
            // So for 'AttendanceCheckin', we might need a more granular check or 'manage-resource'
        });

        // Gate for managing a specific SubGroup
        \Illuminate\Support\Facades\Gate::define('manage-subgroup', function ($user, $subGroupId) {
            if ($user->isSecretary()) {
                return true;
            }
             
            // Check direct assignment
            if ($user->assignments()->where('sub_group_id', $subGroupId)->exists()) {
                return true;
            }

            // Check if they manage the PARENT Department
            $group = \App\Modules\Organization\Models\SubGroup::find($subGroupId);
            if ($group && $user->assignments()->where('department_id', $group->department_id)->exists()) {
                return true;
            }

            return false;
        });
    }
}
