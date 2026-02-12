<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Identity\Models\User;
use App\Modules\Organization\Models\Department;

class AssetAssignment extends Model
{
    protected $fillable = [
        'asset_id',
        'user_id',
        'department_id',
        'assigned_date',
        'return_date_expected',
        'return_date_actual',
        'notes',
        'status',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'return_date_expected' => 'date',
        'return_date_actual' => 'date',
    ];

    /**
     * Get the asset
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the assigned user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Check if assignment is overdue for return
     */
    public function isOverdue(): bool
    {
        return $this->status === 'Active' && $this->return_date_expected && $this->return_date_expected->isPast();
    }
}
