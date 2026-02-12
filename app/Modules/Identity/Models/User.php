<?php

namespace App\Modules\Identity\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Modules\Membership\Models\Member;
use App\Modules\Organization\Models\Department;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function assignments()
    {
        return $this->hasMany(UserAssignment::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'user_assignments')
            ->withPivot('permissions', 'allowed_features')
            ->withTimestamps();
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }
        return !! $role->intersect($this->roles)->count();
    }

    public function hasPermission($permission)
    {
        return $this->roles->flatMap->permissions->contains('slug', $permission);
    }

    public function isSecretary()
    {
        return $this->hasRole('secretary') || $this->hasRole('admin') || $this->hasRole('super-admin');
    }

    public function getManageableDepartmentIds()
    {
        if ($this->hasRole('super-admin') || $this->hasRole('admin') || $this->hasRole('secretary')) {
            return Department::pluck('id')->toArray(); // All departments
        }
        return $this->assignments()->pluck('department_id')->unique()->toArray();
    }

    public function hasAnyDepartmentWithFeature($feature)
    {
        // ... scope checks ...
        // Logic remains same, but maybe simplified if needed.
        if ($this->hasRole('super-admin') || $this->hasRole('admin')) {
             return true; 
        }

        $departmentIds = $this->getManageableDepartmentIds();
        
        if (empty($departmentIds)) return false;

        $departments = Department::whereIn('id', $departmentIds)->get();
        
        return $departments->contains(function ($dept) use ($feature) {
             // Basic Level: Does Dept have feature enabled?
             if (!$dept->hasFeature($feature)) return false;

             // Advanced Level: Does User have permission?
             // If permissions are NULL => Assume Full Access to Dept Features (Backward Compat)
             // If permissions are SET => Check exact key
             return $this->canAccessFeature($dept->id, $feature);
        });
    }

    public function canAccessFeature($departmentId, $feature)
    {
        if ($this->hasRole('super-admin') || $this->hasRole('admin')) {
            return true;
        }

        // 1. Check if Department has feature
        $department = Department::find($departmentId);
        if (!$department || !$department->hasFeature($feature)) {
            return false;
        }

        // 2. Check User Assignment Permissions
        $assignment = $this->assignments()->where('department_id', $departmentId)->first();
        if (!$assignment) {
            return false; // User not assigned to this dept
        }

        // If permissions is null/empty => Assume access to everything enabled in Dept
        if (empty($assignment->permissions)) {
            return true; 
        }

        // If permissions array exists => Must have explicit 'true'
        return isset($assignment->permissions[$feature]) && $assignment->permissions[$feature] === true;
    }

    public function getDepartmentsWithFeature($feature)
    {
        if ($this->hasRole('super-admin') || $this->hasRole('admin')) {
            return Department::whereRaw("JSON_EXTRACT(features, '$.{$feature}') = true")
                ->orWhereRaw("JSON_EXTRACT(settings, '$.{$feature}') = true")
                ->get();
        }

        $departmentIds = $this->assignments()->pluck('department_id')->unique()->toArray();
        
        if (empty($departmentIds)) return collect();

        return Department::whereIn('id', $departmentIds)
            ->where(function($query) use ($feature) {
                $query->whereRaw("JSON_EXTRACT(features, '$.{$feature}') = true")
                      ->orWhereRaw("JSON_EXTRACT(settings, '$.{$feature}') = true");
            })
            ->get()
            ->filter(function($dept) use ($feature) {
                return $this->canAccessFeature($dept->id, $feature);
            });
    }

    public function hasMultipleDepartments()
    {
        if ($this->hasRole('super-admin') || $this->hasRole('admin') || $this->hasRole('secretary')) {
            return Department::where('status', 'active')->count() > 1;
        }
        
        return $this->assignments()->distinct('department_id')->count() > 1;
    }

    public function isStaffUser()
    {
        return !($this->hasRole('super-admin') || $this->hasRole('admin') || $this->hasRole('secretary'));
    }
}
