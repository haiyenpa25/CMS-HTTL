<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Identity\Models\User;

class AssetMaintenance extends Model
{
    protected $fillable = [
        'asset_id',
        'type',
        'description',
        'cost',
        'technician_name',
        'scheduled_date',
        'completion_date',
        'status',
        'reported_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completion_date' => 'date',
        'cost' => 'decimal:2',
    ];

    /**
     * Get the asset
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the reporter
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Check if maintenance is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'Pending' && $this->scheduled_date && $this->scheduled_date->isPast();
    }
}
