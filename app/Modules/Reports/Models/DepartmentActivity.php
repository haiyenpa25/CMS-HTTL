<?php

namespace App\Modules\Reports\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentActivity extends Model
{
    protected $fillable = [
        'report_id',
        'activity_date',
        'name',
        'description',
        'donations_received',
        'attendance',
        'location',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'donations_received' => 'decimal:2',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DepartmentReport::class, 'report_id');
    }
}
