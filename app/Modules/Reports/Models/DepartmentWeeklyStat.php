<?php

namespace App\Modules\Reports\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentWeeklyStat extends Model
{
    protected $fillable = [
        'report_id',
        'week_number',
        'attendance',
        'donations',
    ];

    protected $casts = [
        'donations' => 'decimal:2',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DepartmentReport::class, 'report_id');
    }
}
