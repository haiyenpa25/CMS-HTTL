<?php

namespace App\Config;

class FeatureConfig
{
    /**
     * Get all available features
     */
    public static function getAllFeatures(): array
    {
        return [
            // Attendance Category
            [
                'key' => 'attendance',
                'name' => 'Điểm Danh Chủ Nhật',
                'description' => 'Điểm danh thành viên tham dự buổi nhóm',
                'category' => 'Điểm danh',
                'icon' => 'check-circle',
                'color' => 'blue'
            ],
            [
                'key' => 'group_attendance',
                'name' => 'Điểm danh Buổi Nhóm',
                'description' => 'Điểm danh buổi sinh hoạt nhóm',
                'category' => 'Điểm danh',
                'icon' => 'users',
                'color' => 'green'
            ],
            
            // Care Category
            [
                'key' => 'visits',
                'name' => 'Thăm Viếng',
                'description' => 'Quản lý lịch thăm viếng gia đình',
                'category' => 'Chăm sóc',
                'icon' => 'calendar',
                'color' => 'purple'
            ],
            
            // Management Category
            [
                'key' => 'scheduling',
                'name' => 'Phân Công',
                'description' => 'Phân công nhiệm vụ cho thành viên',
                'category' => 'Quản lý',
                'icon' => 'clipboard',
                'color' => 'orange'
            ],
            [
                'key' => 'inventory',
                'name' => 'Quản lý Tài sản',
                'description' => 'Quản lý tài sản và vật dụng',
                'category' => 'Quản lý',
                'icon' => 'archive',
                'color' => 'gray'
            ],
            
            // Reports Category
            [
                'key' => 'reports',
                'name' => 'Báo Cáo',
                'description' => 'Xem báo cáo hoạt động',
                'category' => 'Báo cáo',
                'icon' => 'chart-bar',
                'color' => 'indigo'
            ],
            [
                'key' => 'report_entry',
                'name' => 'Nhập Báo Cáo',
                'description' => 'Nhập dữ liệu báo cáo',
                'category' => 'Báo cáo',
                'icon' => 'pencil',
                'color' => 'pink'
            ],
            
            // Finance Category
            [
                'key' => 'finance',
                'name' => 'Tài chính',
                'description' => 'Quản lý thu chi tài chính',
                'category' => 'Tài chính',
                'icon' => 'currency-dollar',
                'color' => 'yellow'
            ],
        ];
    }

    /**
     * Get features grouped by category
     */
    public static function getFeaturesByCategory(): array
    {
        $features = self::getAllFeatures();
        $grouped = [];
        
        foreach ($features as $feature) {
            $category = $feature['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $feature;
        }
        
        return $grouped;
    }

    /**
     * Get feature by key
     */
    public static function getFeature(string $key): ?array
    {
        $features = self::getAllFeatures();
        foreach ($features as $feature) {
            if ($feature['key'] === $key) {
                return $feature;
            }
        }
        return null;
    }

    /**
     * Get feature keys only
     */
    public static function getFeatureKeys(): array
    {
        return array_column(self::getAllFeatures(), 'key');
    }
}
