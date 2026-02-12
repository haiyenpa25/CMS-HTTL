<?php

namespace App\Modules\Membership\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Family extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'ward',
        'district',
        'province',
        'latitude',
        'longitude',
        'notes',
    ];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class)->orderBy('visit_date', 'desc');
    }

    // GPS Helper Methods
    public function hasLocation()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function getGoogleMapsUrl()
    {
        if (!$this->hasLocation()) {
            return null;
        }
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }
}
