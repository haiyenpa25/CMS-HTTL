<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Models;

use App\Modules\Membership\Models\{Member, Department};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_session_id', 
        'member_id', 
        'department_id', 
        'sub_group_id', 
        'is_present', 
        'memorized_scripture', 
        'bible_answers_count'
    ];

    protected $casts = [
        'is_present' => 'boolean',
        'memorized_scripture' => 'boolean',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
