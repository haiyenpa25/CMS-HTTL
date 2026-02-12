<?php

namespace App\Modules\Identity\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Organization\Models\Department;
use App\Modules\Organization\Models\SubGroup;

class UserAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'department_id', 'sub_group_id', 'permissions', 'allowed_features'];

    protected $casts = [
        'permissions' => 'array',
        'allowed_features' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function subGroup()
    {
        return $this->belongsTo(SubGroup::class);
    }
}
