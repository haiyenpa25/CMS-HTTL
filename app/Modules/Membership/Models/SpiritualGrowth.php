<?php

namespace App\Modules\Membership\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpiritualGrowth extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'type',
        'event_date',
        'details',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
