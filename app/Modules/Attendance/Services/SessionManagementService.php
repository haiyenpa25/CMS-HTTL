<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Services;

use App\Modules\Attendance\Models\AttendanceSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SessionManagementService
{
    /**
     * Create a single attendance session
     */
    public function createSession(
        string $date,
        string $type = 'sunday_service',
        ?string $name = null,
        ?int $departmentId = null,
        ?int $speakerId = null,
        string $status = 'open'
    ): AttendanceSession {
        return AttendanceSession::create([
            'date' => $date,
            'type' => $type,
            'name' => $name ?? $this->generateSessionName($type, $date),
            'department_id' => $departmentId,
            'speaker_id' => $speakerId,
            'status' => $status,
            'access_scope' => $departmentId ? 'department' : 'global',
        ]);
    }

    /**
     * Create bulk sessions for Sundays within a date range
     */
    public function createBulkSundaySessions(
        string $startDate,
        string $endDate,
        string $type = 'sunday_service',
        ?string $nameTemplate = null
    ): int {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $current = $start->copy();
        $count = 0;

        while ($current <= $end) {
            if ($current->isSunday()) {
                AttendanceSession::firstOrCreate(
                    [
                        'date' => $current->format('Y-m-d'),
                        'type' => $type,
                    ],
                    [
                        'name' => $nameTemplate ?? $this->generateSessionName($type, $current->format('Y-m-d')),
                        'status' => 'open',
                        'access_scope' => 'global',
                    ]
                );
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }

    /**
     * Get sessions with eager loaded relationships
     */
    public function getSessionsWithRelations(?int $departmentId = null, int $limit = 20): Collection
    {
        $query = AttendanceSession::with(['department', 'speaker'])
            ->withSum('summaries', 'total_present')
            ->latest('date');

        if ($departmentId) {
            $query->where(function ($q) use ($departmentId) {
                $q->whereNull('department_id') // Global sessions
                  ->orWhere('department_id', $departmentId);
            });
        }

        return $query->take($limit)->get();
    }

    /**
     * Generate a default session name based on type and date
     */
    private function generateSessionName(string $type, string $date): string
    {
        $carbonDate = Carbon::parse($date);
        
        return match ($type) {
            'sunday_service' => 'Thờ phượng Chúa nhật ' . $carbonDate->format('d/m/Y'),
            'department_meeting' => 'Buổi nhóm ban ngành ' . $carbonDate->format('d/m/Y'),
            default => 'Buổi nhóm ' . $carbonDate->format('d/m/Y'),
        };
    }

    /**
     * Lock a session to prevent further edits
     */
    public function lockSession(int $sessionId): bool
    {
        $session = AttendanceSession::findOrFail($sessionId);
        $session->update(['status' => 'locked']);
        return true;
    }
}
