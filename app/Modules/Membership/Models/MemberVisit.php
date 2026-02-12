<?php

namespace App\Modules\Membership\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'department_id',
        'created_by',
        'category_id',
        'visit_date',
        'scheduled_date',
        'status',
        'visit_type',
        'priority',
        'purpose',
        'notes',
        'prayer_requests',
        'outcome',
        'duration_minutes',
        'completed_at',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'scheduled_date' => 'date',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(VisitCategory::class, 'category_id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'visit_participants', 'visit_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePlanned($query)
    {
        return $query->where('status', 'planned');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'planned')
            ->where('scheduled_date', '<', now());
    }

    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    // Helper methods
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPlanned()
    {
        return $this->status === 'planned';
    }

    public function isOverdue()
    {
        return $this->status === 'planned' && $this->scheduled_date && $this->scheduled_date->isPast();
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'planned' => 'bg-blue-100 text-blue-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            'rescheduled' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPriorityBadgeClass()
    {
        return match($this->priority) {
            'high' => 'bg-red-100 text-red-800',
            'normal' => 'bg-blue-100 text-blue-800',
            'low' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
