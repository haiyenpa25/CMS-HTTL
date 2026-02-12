<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Identity\Models\User;

class AssetTicket extends Model
{
    protected $fillable = [
        'code',
        'asset_id',
        'type',
        'priority',
        'status',
        'issue_description',
        'images',
        'technician_notes',
        'cost',
        'reported_by',
        'assigned_to',
        'completed_at',
    ];

    protected $casts = [
        'images' => 'array',
        'cost' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
