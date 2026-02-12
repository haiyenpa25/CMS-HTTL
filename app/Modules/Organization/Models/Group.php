<?php

namespace App\Modules\Organization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes, \App\Traits\HasGroupFeatures;

    protected $fillable = [
        'name',
        'type',
        'description',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function members()
    {
        return $this->belongsToMany(Member::class, 'group_member')
            ->withPivot('role', 'sub_group')
            ->withTimestamps();
    }

    public function subGroups()
    {
        return $this->hasMany(SubGroup::class);
    }
}
