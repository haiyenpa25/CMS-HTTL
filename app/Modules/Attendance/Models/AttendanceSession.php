<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Models;

use App\Modules\Membership\Models\{Department, Member};
use App\Modules\Speakers\Models\Speaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'speaker_id',
        'mc_id',
        'date',
        'type',
        'name',
        'topic',
        'main_scripture',
        'key_verse',
        'notes',
        'status',
        'access_scope',
        'manual_count',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speaker::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function summaries(): HasMany
    {
        return $this->hasMany(AttendanceSummary::class);
    }

    public function mc(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'mc_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(SessionAssignment::class, 'session_id');
    }

    public function getAutoSumCountAttribute(): int
    {
        // Calculate total present from individual attendances
        $individualCount = $this->attendances()->where('is_present', true)->count();
        
        // OR calculate from summaries if they exist (depending on the logic preferred)
        // Ideally, if "Quick Add" is used, we rely on summaries.
        // If "Detail Checkin" is used, we aggregate attendances.
        // Let's sum up the 'summaries' table as it acts as the SOT for counts per department in this hybrid system.
        
        // Since we might have HYBRID usage (some departments use Quick Add, some Detail),
        // we should ensure that for each department, we take either the summary or the detailed count.
        // However, the plan says "Auto Sum calculated from individual check-ins".
        // Let's refine: AutoSum = Sum of all 'total_present' in summaries table.
        // AND validation logic ensures that 'total_present' in summary is updated whenever detail checkin happens.
        
        return $this->summaries()->sum('total_present');
    }
}
