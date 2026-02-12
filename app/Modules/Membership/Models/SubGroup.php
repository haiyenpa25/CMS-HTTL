<?php

namespace App\Modules\Membership\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'leader_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function leader()
    {
        return $this->belongsTo(Member::class, 'leader_id');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'sub_group_member', 'sub_group_id', 'member_id');
    }
}
