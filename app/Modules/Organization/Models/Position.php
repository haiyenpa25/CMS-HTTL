<?php

namespace App\Modules\Organization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'level',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    /**
     * Get all user assignments with this position
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(\App\Modules\Identity\Models\UserAssignment::class);
    }

    /**
     * Common positions seeder data
     */
    public static function getDefaultPositions(): array
    {
        return [
            ['name' => 'Trưởng ban', 'slug' => 'truong-ban', 'level' => 1],
            ['name' => 'Phó ban', 'slug' => 'pho-ban', 'level' => 2],
            ['name' => 'Thư ký', 'slug' => 'thu-ky', 'level' => 3],
            ['name' => 'Thủ quỹ', 'slug' => 'thu-quy', 'level' => 4],
            ['name' => 'Giáo viên', 'slug' => 'giao-vien', 'level' => 5],
            ['name' => 'Thành viên', 'slug' => 'thanh-vien', 'level' => 10],
        ];
    }
}
