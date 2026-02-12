<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Models;

use App\Modules\Membership\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionAssignment extends Model
{
    protected $fillable = [
        'session_id',
        'member_id',
        'role_name',
        'note',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'session_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
