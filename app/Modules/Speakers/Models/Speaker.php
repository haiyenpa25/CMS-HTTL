<?php

declare(strict_types=1);

namespace App\Modules\Speakers\Models;

use App\Modules\Attendance\Models\AttendanceSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speaker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'phone',
        'email',
        'church_affiliation', // Changed from 'organization'
        'bio',
        'avatar_url',
        'specialties',
        'status',
    ];

    protected $casts = [
        'specialties' => 'array',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }
}
