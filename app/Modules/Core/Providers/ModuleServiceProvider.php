<?php

namespace App\Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use App\Modules\Identity\Livewire\UserManagement;
use App\Modules\Membership\Livewire\MemberManagement;
use App\Modules\Membership\Livewire\FamilyManagement;
use App\Modules\Organization\Livewire\GroupManagement;
use App\Modules\Organization\Livewire\SubGroupManagement;
use App\Modules\Organization\Livewire\DepartmentManagement;
use App\Modules\Organization\Livewire\PositionManagement;
use App\Modules\Attendance\Livewire\AttendanceDashboard;
use App\Modules\Assets\Livewire\AssetDashboard;
use App\Modules\Assets\Livewire\AssetManagement;
use App\Modules\Assets\Livewire\QuickIncidentReport;
use App\Modules\Assets\Livewire\MaintenanceSchedule;
use App\Modules\Speakers\Livewire\SpeakerManager;
use App\Modules\Attendance\Livewire\SessionManager;
use App\Modules\Attendance\Livewire\SessionCreate;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge config if needed, but we used config/modules.php directly
    }

    public function boot()
    {
        $this->registerLivewireComponents();
    }

    protected function registerLivewireComponents()
    {
        // Identity
        Livewire::component('user-management', UserManagement::class);
        Livewire::component('user-permission-assignment', \App\Modules\Identity\Livewire\UserPermissionAssignment::class);
        // Livewire::component('user-dashboard', UserDashboard::class); // If exists

        // Membership
        Livewire::component('member-management', MemberManagement::class);
        Livewire::component('family-management', FamilyManagement::class);

        // Organization
        Livewire::component('group-management', GroupManagement::class);
        Livewire::component('sub-group-management', SubGroupManagement::class);
        Livewire::component('department-management', DepartmentManagement::class);
        Livewire::component('position-management', PositionManagement::class);

        // Attendance
        Livewire::component('attendance-dashboard', AttendanceDashboard::class);

        // Assets
        Livewire::component('asset-dashboard', AssetDashboard::class);
        Livewire::component('asset-management', AssetManagement::class);
        Livewire::component('quick-incident-report', QuickIncidentReport::class);
        Livewire::component('procurement-requests', ProcurementRequests::class);
        Livewire::component('maintenance-schedule', MaintenanceSchedule::class);

        // Speakers
        Livewire::component('speaker-manager', SpeakerManager::class);
        
        // Attendance - Session Manager
        Livewire::component('session-manager', SessionManager::class);
        Livewire::component('session-create', SessionCreate::class);
    }
}
