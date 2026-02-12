<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Modular Architecture Configuration
    |--------------------------------------------------------------------------
    |
    | Define your modules with 2-level nested menu structure.
    | Each module can have 'submenu' key for grouping related items.
    |
    */

    'modules' => [
        'organization' => [
            'name' => 'Quản lý Tổ chức',
            'active' => true,
            'order' => 1,
            'icon' => 'office-building',
            'submenu' => [
                [
                    'text' => 'Nhân sự',
                    'icon' => 'user-group',
                    'items' => [
                        [
                            'text' => 'Thành viên',
                            'route' => 'members.index',
                        ],
                        [
                            'text' => 'Gia đình',
                            'route' => 'families.index',
                        ],
                        [
                            'text' => 'Phân bổ nhân sự',
                            'route' => 'assignments.index',
                        ],
                    ],
                ],
                [
                    'text' => 'Cơ cấu',
                    'icon' => 'view-grid-add',
                    'items' => [
                        [
                            'text' => 'Ban ngành',
                            'route' => 'departments.index',
                        ],
                        [
                            'text' => 'Nhóm / Tổ',
                            'route' => 'groups.index',
                        ],
                        [
                            'text' => 'Chức vụ',
                            'route' => 'positions.index',
                        ],
                        [
                            'text' => 'Tổ nhỏ',
                            'route' => 'sub-groups.index',
                        ],
                    ],
                ],
            ],
        ],
        'activities' => [
            'name' => 'Hoạt động',
            'active' => true,
            'order' => 2,
            'icon' => 'calendar',
            'submenu' => [
                [
                    'text' => 'Điểm danh',
                    'icon' => 'clipboard-check',
                    'items' => [
                        [
                            'text' => 'Buổi nhóm Hội thánh',
                            'route' => 'attendance.dashboard',
                        ],
                        [
                            'text' => 'Buổi nhóm ban ngành',
                            'route' => 'attendance.group',
                        ],
                    ],
                ],
                [
                    'text' => 'Thăm viếng',
                    'icon' => 'heart',
                    'items' => [
                        [
                            'text' => 'Lịch trình',
                            'route' => 'visits.index',
                        ],
                        [
                            'text' => 'Lịch sử',
                            'route' => 'visits.members',
                        ],
                    ],
                ],
                [
                    'text' => 'Tài sản',
                    'icon' => 'cube',
                    'items' => [
                        [
                            'text' => 'Quản lý tài sản',
                            'route' => 'assets.index',
                        ],
                        [
                            'text' => 'Trung tâm Bảo trì',
                            'route' => 'assets.maintenance',
                        ],
                        [
                            'text' => 'Mua sắm & Đề xuất',
                            'route' => 'assets.procurement',
                        ],
                    ],
                ],
            ],
        ],
        'reports' => [
            'name' => 'Báo cáo & Hệ thống',
            'active' => true,
            'order' => 4,
            'icon' => 'chart-bar',
            'submenu' => [
                [
                    'text' => 'Thống kê',
                    'icon' => 'presentation-chart-line',
                    'items' => [
                        [
                            'text' => 'Tổng hợp',
                            'route' => 'reports.index',
                        ],
                        [
                            'text' => 'Báo cáo ban ngành sinh hoạt',
                            'route' => 'reports.department',
                        ],
                    ],
                ],
                [
                    'text' => 'Cấu hình',
                    'icon' => 'cog',
                    'items' => [
                        [
                            'text' => 'Quản trị viên',
                            'route' => 'users.index',
                            'permission' => 'manage-users',
                        ],
                        [
                            'text' => 'Phân quyền Ban ngành',
                            'route' => 'admin.features',
                            'permission' => 'manage-departments',
                        ],
                        [
                            'text' => 'Quản lý Diễn giả',
                            'route' => 'speakers.index',
                        ],
                    ],
                ],
            ],
        ],
        'sessions' => [
            'name' => 'Buổi nhóm',
            'active' => true,
            'order' => 5,
            'icon' => 'calendar',
            'submenu' => [
                [
                    'text' => 'Tác vụ',
                    'icon' => 'plus-circle',
                    'items' => [
                        [
                            'text' => 'Tạo buổi nhóm',
                            'route' => 'sessions.create',
                        ],
                        [
                            'text' => 'Quản lý buổi nhóm',
                            'route' => 'sessions.index',
                        ],
                    ],
                ],
            ],
        ],
    ]
];
