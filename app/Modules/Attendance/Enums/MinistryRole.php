<?php

namespace App\Modules\Attendance\Enums;

class MinistryRole
{
    public const ROLES = [
        'Ban Nhạc' => [
            'Đàn Piano',
            'Đàn Guitar',
            'Đàn Organ',
            'Trống',
            'Bass',
            'Ca sĩ',
        ],
        'Ban Tiếp tân' => [
            'Tiếp tân',
            'Hướng dẫn',
            'Đón khách',
        ],
        'Ban Kỹ thuật' => [
            'Âm thanh',
            'Hình ảnh',
            'Livestream',
            'Chiếu',
        ],
        'Phụ lễ' => [
            'Phụ lễ',
            'Đọc Kinh Thánh',
            'Cầu nguyện',
        ],
        'Khác' => [
            'Dọn dẹp',
            'Pha chế',
        ],
    ];

    public static function getAllRoles(): array
    {
        $flat = [];
        foreach (self::ROLES as $category => $roles) {
            foreach ($roles as $role) {
                $flat[] = $role;
            }
        }
        return $flat;
    }

    public static function getRolesByCategory(): array
    {
        return self::ROLES;
    }
}
