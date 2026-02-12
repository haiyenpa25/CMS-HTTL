<?php

namespace App\Helpers;

use App\Modules\Identity\Models\User;
use Illuminate\Support\Collection;

class MenuHelper
{
    /**
     * Get list of available features for a user
     */
    public static function getAvailableFeatures(User $user): array
    {
        // Admin/Super-admin have access to everything
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            return [
                'attendance',
                'group_attendance',
                'visits',
                'scheduling',
                'reports',
                'report_entry',
                'inventory',
                'finance',
                'members',
                'departments',
                'users',
                'settings'
            ];
        }

        $features = [];
        
        // Get all user assignments with allowed_features
        $assignments = $user->assignments()->with('department')->get();
        
        foreach ($assignments as $assignment) {
            // Use allowed_features from the new 2-phase system
            $allowedFeatures = $assignment->allowed_features ?? [];
            
            foreach ($allowedFeatures as $feature) {
                if (!in_array($feature, $features)) {
                    $features[] = $feature;
                }
            }
        }
        
        return $features;
    }

    /**
     * Get departments user is assigned to
     */
    public static function getUserDepartments(User $user): Collection
    {
        if ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('secretary')) {
            return \App\Modules\Organization\Models\Department::where('status', 'active')->get();
        }
        
        return $user->assignments()
            ->with('department')
            ->get()
            ->pluck('department')
            ->filter();
    }

    /**
     * Check if a specific menu should be shown
     */
    public static function shouldShowMenu(User $user, string $menuKey): bool
    {
        // Admin menus
        $adminMenus = ['members', 'departments', 'users', 'settings', 'titles'];
        if (in_array($menuKey, $adminMenus)) {
            return $user->hasRole('super-admin') || 
                   $user->hasRole('admin') || 
                   $user->hasRole('secretary');
        }

        // Feature-based menus
        $features = self::getAvailableFeatures($user);
        
        $menuFeatureMap = [
            'attendance' => 'attendance',
            'group_attendance' => 'group_attendance',
            'visits' => 'visits',
            'scheduling' => 'scheduling',
            'reports' => 'reports',
            'report_entry' => 'report_entry',
            'inventory' => 'inventory',
        ];

        if (isset($menuFeatureMap[$menuKey])) {
            return in_array($menuFeatureMap[$menuKey], $features);
        }

        // Dashboard is always visible
        if ($menuKey === 'dashboard') {
            return true;
        }

        return false;
    }

    /**
     * Check if user is a staff user (not admin)
     */
    public static function isStaffUser(User $user): bool
    {
        return !($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('secretary'));
    }

    /**
     * Get menu items for user
     */
    public static function getMenuItems(User $user): array
    {
        $isStaff = self::isStaffUser($user);
        
        if ($isStaff) {
            // Staff users see only operational menus
            return self::getStaffMenuItems($user);
        } else {
            // Admin users see full menu
            return self::getAdminMenuItems($user);
        }
    }

    private static function getStaffMenuItems(User $user): array
    {
        $features = self::getAvailableFeatures($user);
        $items = [];

        // Dashboard (always visible)
        $items[] = [
            'key' => 'dashboard',
            'label' => 'Tổng quan',
            'icon' => 'dashboard',
            'route' => 'dashboard',
            'visible' => true
        ];

        // Operational menus based on permissions
        if (in_array('attendance', $features)) {
            $items[] = [
                'key' => 'attendance',
                'label' => 'Điểm Danh Chủ Nhật',
                'icon' => 'check-circle',
                'route' => 'attendance.sunday',
                'visible' => true
            ];
        }

        if (in_array('group_attendance', $features)) {
            $items[] = [
                'key' => 'group_attendance',
                'label' => 'Điểm danh Buổi Nhóm',
                'icon' => 'users',
                'route' => 'attendance.group',
                'visible' => true
            ];
        }

        if (in_array('visits', $features)) {
            $items[] = [
                'key' => 'visits',
                'label' => 'Thăm Viếng',
                'icon' => 'calendar',
                'route' => 'visits.index',
                'visible' => true
            ];
        }

        if (in_array('scheduling', $features)) {
            $items[] = [
                'key' => 'scheduling',
                'label' => 'Phân Công',
                'icon' => 'clipboard',
                'route' => 'scheduling.index',
                'visible' => true
            ];
        }

        if (in_array('reports', $features)) {
            $items[] = [
                'key' => 'reports',
                'label' => 'Báo Cáo',
                'icon' => 'chart',
                'route' => 'reports.index',
                'visible' => true
            ];
        }

        if (in_array('report_entry', $features)) {
            $items[] = [
                'key' => 'report_entry',
                'label' => 'Nhập Báo Cáo',
                'icon' => 'edit',
                'route' => 'reports.entry',
                'visible' => true
            ];
        }

        return $items;
    }

    private static function getAdminMenuItems(User $user): array
    {
        // Return full admin menu structure
        return [
            [
                'key' => 'dashboard',
                'label' => 'Tổng quan',
                'icon' => 'dashboard',
                'route' => 'dashboard',
                'visible' => true
            ],
            // Add all admin menu items here
            // This will be used by the sidebar component
        ];
    }
}
