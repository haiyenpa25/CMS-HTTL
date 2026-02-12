<?php

namespace App\Services;

use App\Modules\Attendance\Models\AttendanceSession;
use App\Models\Visit;
use App\Modules\Organization\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportStatsService
{
    /**
     * Get attendance count for a specific department and date range.
     * Often reports are for a specific Sunday or Month.
     */
    public function getAttendanceData(int $departmentId, $startDate, $endDate)
    {
        // Simple distinct count of members present in sessions within range
        return AttendanceSession::where('department_id', $departmentId)
            ->whereBetween('date', [$startDate, $endDate])
            ->withCount(['attendances' => function ($query) {
                $query->where('status', 'dihoc'); // Assuming 'dihoc' is the present status
            }])
            ->get()
            ->sum('attendances_count');
    }

    /**
     * Get completed visits for the department's members within date range.
     */
    public function getCompletedVisits(int $departmentId, $startDate, $endDate)
    {
        // Get members of the department
        // Then get visits for those members
        // Assuming we want visits DONE by this department or FOR members of this department?
        // Usually reports focus on "Cong tac tham vieng" -> Visits performed.
        // But visits are linked to Members (receivers). 
        // Let's count visits where the visitor (if we tracked it) or just visits for members in this dept.
        // For now, let's get visits for members belonging to this department.
        
        return Visit::whereHas('member', function ($q) use ($departmentId) {
                $q->whereHas('departments', function ($dq) use ($departmentId) {
                    $dq->where('departments.id', $departmentId);
                });
            })
            ->where('status', 'completed')
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->get();
    }

    /**
     * Get growth chart data (attendance trend) for the last 12 reports/sessions.
     */
    public function getGrowthChartData(int $departmentId)
    {
        // Option 1: Get from past Reports if they exist
        // Option 2: Calculate from AttendanceSessions directly (more accurate if reports are missing)
        
        // Let's use AttendanceSessions for "Real data"
        $sessions = AttendanceSession::where('department_id', $departmentId)
            ->orderBy('date', 'asc')
            ->limit(12) // Last 12 sessions
            ->withCount(['attendances' => function ($query) {
                $query->where('status', 'dihoc');
            }])
            ->get();

        return [
            'labels' => $sessions->map(function ($session) {
                return $session->date ? Carbon::parse($session->date)->format('d/m') : 'N/A';
            })->toArray(),
            'data' => $sessions->pluck('attendances_count')->toArray(),
        ];
    }
}
