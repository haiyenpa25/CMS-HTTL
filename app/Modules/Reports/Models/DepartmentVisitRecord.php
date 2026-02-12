<?php

namespace App\Modules\Reports\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Membership\Models\Member;

class DepartmentVisitRecord extends Model
{
    protected $fillable = [
        'report_id',
        'member_id',
        'visit_date',
        'visit_type',
        'notes',
        'status',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DepartmentReport::class, 'report_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function getVisitTypeNameAttribute(): string
    {
        $types = [
            'sick' => 'Thăm đau ốm',
            'new_believer' => 'Chăm sóc sau tin Chúa',
            'follow_up' => 'Tín hữu mới',
            'encouragement' => 'Động viên',
        ];
        return $types[$this->visit_type] ?? $this->visit_type;
    }
}
