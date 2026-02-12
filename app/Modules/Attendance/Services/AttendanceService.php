<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Services;

use App\Modules\Attendance\Models\{Attendance, AttendanceSession};
use Illuminate\Support\Collection;

class AttendanceService
{
    /**
     * Record or update attendance for a member in a session
     */
    public function recordAttendance(
        int $sessionId,
        int $memberId,
        bool $isPresent = true,
        ?int $departmentId = null,
        ?int $subGroupId = null,
        bool $memorizedScripture = false,
        int $bibleAnswersCount = 0
    ): Attendance {
        return Attendance::updateOrCreate(
            [
                'attendance_session_id' => $sessionId,
                'member_id' => $memberId,
            ],
            [
                'is_present' => $isPresent,
                'department_id' => $departmentId,
                'sub_group_id' => $subGroupId,
                'memorized_scripture' => $memorizedScripture,
                'bible_answers_count' => $bibleAnswersCount,
            ]
        );
    }

    /**
     * Get all attendances for a session with eager loaded relationships
     */
    public function getSessionAttendances(int $sessionId): Collection
    {
        return Attendance::with(['member', 'department'])
            ->where('attendance_session_id', $sessionId)
            ->get();
    }

    /**
     * Get attendance statistics for a session
     */
    public function getSessionStatistics(int $sessionId): array
    {
        $attendances = $this->getSessionAttendances($sessionId);
        
        return [
            'total' => $attendances->count(),
            'present' => $attendances->where('is_present', true)->count(),
            'absent' => $attendances->where('is_present', false)->count(),
            'memorized_scripture' => $attendances->where('memorized_scripture', true)->count(),
            'total_bible_answers' => $attendances->sum('bible_answers_count'),
        ];
    }

    /**
     * Bulk record attendances for multiple members
     */
    public function bulkRecordAttendances(int $sessionId, array $memberIds, bool $isPresent = true): int
    {
        $count = 0;
        foreach ($memberIds as $memberId) {
            $this->recordAttendance($sessionId, $memberId, $isPresent);
            $count++;
        }
        return $count;
    }
}
