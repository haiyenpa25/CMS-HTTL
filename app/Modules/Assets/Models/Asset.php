<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\Organization\Models\Department;
use App\Modules\Identity\Models\User;
use App\Modules\Membership\Models\Member;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'brand',
        'model',
        'manufacturer',
        'serial_number',
        'purchase_date',
        'warranty_expiry',
        'price',
        'current_value',
        'status',
        'location_id',
        'reserved_location', // if needed later
        'description',
        'qr_code',
        'next_maintenance_date',
        'replaced_by_asset_id',
        'managed_by',
        'used_by_member_id',
        'manual_url',
        'image_url',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'next_maintenance_date' => 'date',
        'price' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class);
    }

    /**
     * Get the location (department)
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'location_id');
    }

    /**
     * Get person in charge
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    /**
     * Get member using the asset
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'used_by_member_id');
    }

    /**
     * Get maintenance tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(AssetTicket::class);
    }

    /**
     * Get maintenance schedules
     */
    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(AssetMaintenanceSchedule::class);
    }

    /**
     * Get assignment history (Legacy or if kept)
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    /**
     * Get replacement asset
     */
    public function replacedBy(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'replaced_by_asset_id');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Active' => 'success',
            'Repairing' => 'warning',
            'Broken' => 'danger',
            'Lost' => 'secondary',
            'Disposed' => 'dark',
            default => 'primary',
        };
    }
}
