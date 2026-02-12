<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\Identity\Models\User;
use App\Modules\Organization\Models\Department;

class AssetProcurement extends Model
{
    protected $fillable = [
        'title',
        'code',
        'requester_id',
        'department_id',
        'reason',
        'status',
        'approved_by',
        'total_estimated_cost',
        'approved_at',
    ];

    protected $casts = [
        'total_estimated_cost' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AssetProcurementItem::class, 'procurement_id');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(AssetProcurementQuote::class, 'procurement_id');
    }
}
