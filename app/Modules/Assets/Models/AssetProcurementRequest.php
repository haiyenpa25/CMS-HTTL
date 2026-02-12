<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Identity\Models\User;
use App\Modules\Organization\Models\Department;

class AssetProcurementRequest extends Model
{
    protected $fillable = [
        'requested_by',
        'department_id',
        'item_name',
        'category_id',
        'quantity',
        'estimated_price',
        'justification',
        'priority',
        'status',
        'approved_by',
        'approved_date',
        'rejection_reason',
        'finance_expense_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'estimated_price' => 'decimal:2',
        'approved_date' => 'date',
    ];

    /**
     * Get the requester
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the department
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class);
    }

    /**
     * Get the approver
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if request is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    /**
     * Approve the request
     */
    public function approve(User $approver): void
    {
        $this->update([
            'status' => 'Approved',
            'approved_by' => $approver->id,
            'approved_date' => now(),
        ]);
    }

    /**
     * Reject the request
     */
    public function reject(User $approver, string $reason): void
    {
        $this->update([
            'status' => 'Rejected',
            'approved_by' => $approver->id,
            'approved_date' => now(),
            'rejection_reason' => $reason,
        ]);
    }
}
