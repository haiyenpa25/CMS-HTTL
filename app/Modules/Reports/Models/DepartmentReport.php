<?php

namespace App\Modules\Reports\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\Organization\Models\Department;
use App\Models\User;

class DepartmentReport extends Model
{
    protected $fillable = [
        'department_id',
        'year',
        'month',
        'created_by',
        'total_attendance',
        'total_donations',
        'visits_completed',
        'visits_total',
        'new_members',
        'attendance_change_percent',
        'donations_change_percent',
        'general_comments',
        'suggestions',
        'prayer_requests',
        'prayer_topics',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'prayer_topics' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'total_donations' => 'decimal:2',
        'attendance_change_percent' => 'decimal:2',
        'donations_change_percent' => 'decimal:2',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(DepartmentActivity::class, 'report_id');
    }

    public function weeklyStats(): HasMany
    {
        return $this->hasMany(DepartmentWeeklyStat::class, 'report_id');
    }

    public function visitRecords(): HasMany
    {
        return $this->hasMany(DepartmentVisitRecord::class, 'report_id');
    }

    public function getVisitCompletionPercentageAttribute(): int
    {
        if ($this->visits_total == 0) {
            return 0;
        }
        return round(($this->visits_completed / $this->visits_total) * 100);
    }

    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'Tháng 1', 2 => 'Tháng 2', 3 => 'Tháng 3', 4 => 'Tháng 4',
            5 => 'Tháng 5', 6 => 'Tháng 6', 7 => 'Tháng 7', 8 => 'Tháng 8',
            9 => 'Tháng 9', 10 => 'Tháng 10', 11 => 'Tháng 11', 12 => 'Tháng 12',
        ];
        return $months[$this->month] ?? '';
    }
}
