<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetProcurementItem extends Model
{
    protected $fillable = [
        'procurement_id',
        'item_name',
        'specifications',
        'quantity',
        'unit_price_estimate',
        'supplier_url',
    ];

    protected $casts = [
        'unit_price_estimate' => 'decimal:2',
    ];

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(AssetProcurement::class);
    }
}
