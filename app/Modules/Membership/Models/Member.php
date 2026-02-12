<?php

declare(strict_types=1);

namespace App\Modules\Membership\Models;

use App\Modules\Attendance\Models\Attendance;
use App\Modules\Identity\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany, HasOne};

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'family_id',
        'family_role',
        'title_id',
        'full_name',
        'avatar',
        'identity_card',
        'email',
        'phone',
        'gender',
        'note',
        'birthday',
        'blood_group',
        'job',
        'is_married',
        'date_faith',
        'date_baptism',
        'baptized_by',
        'baptism_place',
        'joined_date',
        'referred_by',
        'spiritual_gifts',
        'status',
        'last_visited_at',
        'latitude',
        'longitude',
        'location_updated_at',
        'location_updated_by',
    ];

    protected $casts = [
        'spiritual_gifts' => 'array',
        'job' => 'array',
        'is_married' => 'boolean',
        'birthday' => 'date',
        'date_faith' => 'date',
        'date_baptism' => 'date',
        'joined_date' => 'date',
        'last_visited_at' => 'datetime',
        'location_updated_at' => 'datetime',
    ];

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_member')
            ->withPivot('role', 'sub_group')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include members matching the search term.
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_member')
            ->withPivot('sub_group_id', 'role')
            ->withTimestamps();
    }

    public function memberVisits(): HasMany
    {
        return $this->hasMany(MemberVisit::class);
    }

    /**
     * Scope a query to only include members matching the search term.
     */
    public function scopeSearch(Builder $query, string $term): void
    {
        $query->where(function ($q) use ($term) {
            $q->where('full_name', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }

    public function scopeAgeBetween(Builder $query, int $min, int $max = null): void
    {
        $query->whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) >= ?', [$min]);
        if ($max) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) <= ?', [$max]);
        }
    }

    public function scopeStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }

    public function scopeJoinedBefore(Builder $query, string $date): void
    {
        $query->where('joined_date', '<=', $date);
    }

    public function scopeIsBaptized(Builder $query, bool $baptized = true): void
    {
        if ($baptized) {
            $query->whereNotNull('date_baptism');
        } else {
            $query->whereNull('date_baptism');
        }
    }

    public function scopeInDepartment(Builder $query, int $departmentId): void
    {
        $query->whereHas('departments', function ($q) use ($departmentId) {
            $q->where('departments.id', $departmentId);
        });
    }
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // GPS Helper Methods
    public function hasLocation(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function getGoogleMapsUrl(): ?string
    {
        if (!$this->hasLocation()) {
            return null;
        }
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    public function getDistanceFrom(float $lat, float $lng): ?float
    {
        if (!$this->hasLocation()) {
            return null;
        }

        // Haversine formula for distance calculation in kilometers
        $earthRadius = 6371;

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
