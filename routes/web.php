<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\DashboardOverview;
use App\Modules\Membership\Livewire\MemberManagement;
use App\Livewire\FamilyDetail;

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::get('/', App\Livewire\UserDashboard::class)->name('dashboard');
    Route::get('/members', App\Modules\Membership\Livewire\MemberManagement::class)->name('members.index');
    Route::get('/families', App\Modules\Membership\Livewire\FamilyManagement::class)->name('families.index');
    Route::get('/families/{familyId}', FamilyDetail::class)->name('families.detail');
    Route::get('/members/filter', App\Livewire\MemberFilter::class)->name('members.filter');
    Route::get('/members/{memberId}', App\Livewire\MemberDetail::class)->name('members.detail');
    Route::get('/groups', App\Modules\Organization\Livewire\GroupManagement::class)->name('groups.index');
    Route::get('/departments', App\Modules\Organization\Livewire\DepartmentManagement::class)->name('departments.index');
    Route::get('/sub-groups', App\Modules\Organization\Livewire\SubGroupManagement::class)->name('sub-groups.index');
    Route::get('/positions', App\Modules\Organization\Livewire\PositionManagement::class)->name('positions.index');
    Route::get('/assignments', App\Livewire\MemberAssignment::class)->name('assignments.index');
    Route::get('/attendance', App\Modules\Attendance\Livewire\AttendanceDashboard::class)->name('attendance.dashboard');
    Route::get('/attendance/{sessionId}/checkin', App\Livewire\AttendanceCheckin::class)->name('attendance.checkin');
    Route::get('/attendance/group', App\Livewire\DepartmentAttendance::class)->name('attendance.group');
    Route::get('/visits', App\Livewire\VisitDashboard::class)->name('visits.index');
    Route::get('/visits/members', App\Livewire\MemberVisitList::class)->name('visits.members');
    
    // Assets
    Route::get('/assets', App\Modules\Assets\Livewire\AssetDashboard::class)->name('assets.dashboard');
    Route::get('/assets/manage', App\Modules\Assets\Livewire\AssetManagement::class)->name('assets.index');
    Route::get('/assets/maintenance', App\Modules\Assets\Livewire\AssetMaintenanceCenter::class)->name('assets.maintenance');
    Route::get('/assets/procurement', App\Modules\Assets\Livewire\AssetProcurement::class)->name('assets.procurement');
    Route::get('/assets/{asset}/report-incident', App\Modules\Assets\Livewire\QuickIncidentReport::class)->name('assets.report-incident');
    
    // Reports
    Route::get('/reports', App\Livewire\Report\ReportDashboard::class)->name('reports.index');
    Route::get('/reports/department', App\Modules\Reports\Livewire\DepartmentReportDashboard::class)->name('reports.department');
    Route::get('/reports/{report}', App\Livewire\Report\ReportDetail::class)->name('reports.detail');
    
    // User Management (Admin only)
    Route::get('/users', App\Modules\Identity\Livewire\UserManagement::class)->name('users.index');
    Route::get('/admin/features', App\Livewire\Admin\DepartmentFeatures::class)->name('admin.features');

    // Speakers
    Route::get('/speakers', App\Modules\Speakers\Livewire\SpeakerManager::class)->name('speakers.index');
    
    // Session Management
    Route::get('/sessions', App\Modules\Attendance\Livewire\SessionManager::class)->name('sessions.index');
    Route::get('/sessions/create', App\Modules\Attendance\Livewire\SessionCreate::class)->name('sessions.create');
});

require __DIR__.'/auth.php';
