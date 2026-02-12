<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'maintenance_interval_days',
        'description',
    ];

    protected $casts = [
        'maintenance_interval_days' => 'integer',
    ];

    /**
     * Get all assets in this category
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id');
    }

    /**
     * Get procurement requests for this category
     */
    public function procurementRequests(): HasMany
    {
        return $this->hasMany(AssetProcurementRequest::class, 'category_id');
    }

    /**
     * Default categories for seeding
     */
    public static function getDefaultCategories(): array
    {
        return [
            ['name' => 'Máy tính', 'slug' => 'may-tinh', 'maintenance_interval_days' => 180, 'description' => 'Máy tính để bàn, laptop'],
            ['name' => 'Máy chiếu', 'slug' => 'may-chieu', 'maintenance_interval_days' => 90, 'description' => 'Máy chiếu, projector'],
            ['name' => 'Âm thanh', 'slug' => 'am-thanh', 'maintenance_interval_days' => 60, 'description' => 'Loa, micro, mixer'],
            ['name' => 'Thiết bị văn phòng', 'slug' => 'thiet-bi-van-phong', 'maintenance_interval_days' => 120, 'description' => 'Máy in, máy photocopy'],
            ['name' => 'Nội thất', 'slug' => 'noi-that', 'maintenance_interval_days' => 365, 'description' => 'Bàn ghế, tủ'],
            ['name' => 'Khác', 'slug' => 'khac', 'maintenance_interval_days' => 90, 'description' => 'Thiết bị khác'],
        ];
    }
}
