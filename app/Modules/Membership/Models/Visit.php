<?php

namespace App\Modules\Membership\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'family_id',
        'department_id',
        'created_by',
        'visit_date',
        'visit_type',
        'priority',
        'status',
        'reason',
        'prayer_needs',
        'visit_notes',
        'weeks_absent',
        'months_since_last_visit',
        'visitors', // Keep for backward compatibility
    ];

    protected $casts = [
        'visit_date' => 'date',
        'visitors' => 'array',
    ];

    // Relationships
    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'visit_participants')
            ->withPivot('role')
            ->withTimestamps();
    }

    // Scopes for Smart Categorization
    public function scopeSos($query)
    {
        return $query->where('priority', 'sos')
            ->orderBy('visit_date', 'asc');
    }

    public function scopeSuggested($query)
    {
        return $query->where('visit_type', 'suggested')
            ->where('status', 'planned')
            ->orderBy('weeks_absent', 'desc')
            ->orderBy('months_since_last_visit', 'desc');
    }

    public function scopePlanned($query)
    {
        return $query->where('status', 'planned')
            ->orderBy('visit_date', 'asc');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')
            ->orderBy('visit_date', 'desc');
    }

    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    // Helper Methods
    public function isSos()
    {
        return $this->priority === 'sos';
    }

    public function isSuggested()
    {
        return $this->visit_type === 'suggested';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function getPriorityBadgeClass()
    {
        return match($this->priority) {
            'sos' => 'bg-red-100 text-red-800',
            'high' => 'bg-orange-100 text-orange-800',
            'normal' => 'bg-blue-100 text-blue-800',
            'low' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'planned' => 'bg-yellow-100 text-yellow-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
