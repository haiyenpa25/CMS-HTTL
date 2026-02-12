<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'department_id',
        'user_id',
        'type',
        'reporting_date',
        'attendance_count',
        'content',
        'status',
    ];

    protected $casts = [
        'reporting_date' => 'date',
        'content' => 'array',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
