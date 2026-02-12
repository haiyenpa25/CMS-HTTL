<?php

namespace App\Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSummary extends Model
{
    use HasFactory;

    protected $fillable = ['attendance_session_id', 'department_id', 'total_present', 'is_manual_entry'];

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
