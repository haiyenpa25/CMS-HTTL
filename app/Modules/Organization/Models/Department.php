<?php

namespace App\Modules\Organization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'department_type',
        'description',
        'type',
        'features',
        'settings',
        'status',
    ];

    protected $casts = [
        'features' => 'array',
        'settings' => 'array',
    ];

    /**
     * Get all sub-groups for this department.
     */
    public function subGroups()
    {
        return $this->hasMany(SubGroup::class);
    }

    /**
     * Check if the department has a specific feature enabled.
     * Checks both 'settings' and legacy 'features' columns.
     */
    public function hasFeature(string $feature): bool
    {
        $settings = $this->settings ?? [];
        if (isset($settings[$feature]) && $settings[$feature] === true) {
            return true;
        }

        $features = $this->features ?? [];
        return isset($features[$feature]) && $features[$feature] === true;
    }

    /**
     * Enable a specific feature.
     * Writes to 'settings' column.
     */
    public function enableFeature(string $feature): void
    {
        $settings = $this->settings ?? [];
        $settings[$feature] = true;
        $this->settings = $settings;
        $this->save();
    }

    /**
     * Disable a specific feature.
     * Writes to 'settings' column.
     */
    public function disableFeature(string $feature): void
    {
        $settings = $this->settings ?? [];
        if (isset($settings[$feature])) {
            $settings[$feature] = false; // Explicitly set to false
            $this->settings = $settings;
            $this->save();
        }
    }
}
