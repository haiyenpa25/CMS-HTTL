<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMaintenanceSchedule extends Model
{
    protected $fillable = [
        'asset_id',
        'frequency_type',
        'interval',
        'last_performed_at',
        'next_due_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'last_performed_at' => 'date',
        'next_due_at' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
