# Batch update imports from Membership to Organization
$files = @(
    "app\Helpers\MenuHelper.php",
    "app\Services\ReportStatsService.php",
    "app\Livewire\AttendanceCheckin.php",
    "app\Livewire\DepartmentAttendance.php",
    "app\Livewire\DepartmentFeatureRegistry.php",
    "app\Livewire\MemberAssignment.php",
    "app\Livewire\MemberFilter.php",
    "app\Livewire\MemberVisitList.php",
    "app\Livewire\VisitDashboard.php",
    "app\Livewire\VisitManagement.php",
    "app\Livewire\DashboardOverview.php",
    "app\Livewire\Admin\DepartmentFeatures.php",
    "app\Livewire\Report\CreateReportForm.php",
    "app\Livewire\Report\ReportDashboard.php",
    "app\Modules\Identity\Models\User.php",
    "app\Modules\Identity\Livewire\UserPermissionAssignment.php",
    "app\Modules\Identity\Livewire\UserManagement.php",
    "app\Modules\Attendance\Livewire\AttendanceDashboard.php",
    "app\Modules\Membership\Livewire\MemberManagement.php",
    "app\Providers\AppServiceProvider.php"
)

foreach ($file in $files) {
    $path = Join-Path "c:\laragon\www\CMS-HT" $file
    if (Test-Path $path) {
        $content = Get-Content $path -Raw
        $content = $content -replace 'App\\Modules\\Membership\\Models\\Department', 'App\Modules\Organization\Models\Department'
        $content = $content -replace 'App\\Modules\\Membership\\Models\\Group', 'App\Modules\Organization\Models\Group'
        $content = $content -replace 'App\\Modules\\Membership\\Models\\SubGroup', 'App\Modules\Organization\Models\SubGroup'
        Set-Content $path -Value $content -NoNewline
        Write-Host "Updated: $file"
    }
}

Write-Host "Batch update complete!"
