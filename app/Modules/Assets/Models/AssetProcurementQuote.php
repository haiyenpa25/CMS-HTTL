<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetProcurementQuote extends Model
{
    protected $fillable = [
        'procurement_id',
        'supplier_name',
        'total_price',
        'file_url',
        'is_selected',
        'notes',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'is_selected' => 'boolean',
    ];

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(AssetProcurement::class);
    }
}
