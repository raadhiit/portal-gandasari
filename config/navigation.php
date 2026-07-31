<?php

return [
    [
        'heading' => 'Platform',
        'visibility' => ['dashboard.view', 'notification.view'],
        'items' => [
            [
                'label' => 'Dashboard',
                'route' => 'dashboard',
                'active' => 'dashboard',
                'icon' => 'home',
                'permission' => 'dashboard.view',
                'class' => 'rounded-xl text-[#4E5C66] hover:bg-[#E2E8E4] hover:text-[#17201B] data-current:bg-[#EAF4FB] data-current:text-[#2D9CDB] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#1D2B33] dark:data-current:text-[#6EA3BA]',
            ],
            [
                'label' => 'Notifications',
                'route' => 'notifications.index',
                'active' => 'notifications.*',
                'icon' => 'bell',
                'permission' => 'notification.view',
                'class' => 'rounded-xl text-[#4E5C66] hover:bg-[#E2E8E4] hover:text-[#17201B] data-current:bg-[#F8EEF8] data-current:text-[#6B5F8F] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#241F2C] dark:data-current:text-[#A59ADD]',
            ],
        ],
    ],
    [
        'heading' => 'Item Management',
        'visibility' => ['item-request.view', 'item-request.create', 'item-request.validate', 'item-request.account', 'item-request.export'],
        'class' => 'mt-3 [&_h3]:text-[#66736D] dark:[&_h3]:text-[#9BAAB3]',
        'items' => [
            [
                'label' => 'Item Requests',
                'route' => 'item-requests.index',
                'active' => 'item-requests.index',
                'icon' => 'clipboard-document-list',
                'permission' => 'item-request.view',
                'class' => 'rounded-xl text-[#4E5C66] hover:bg-[#E2E8E4] hover:text-[#17201B] data-current:bg-[#EAF4FB] data-current:text-[#2D9CDB] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#1D2B33] dark:data-current:text-[#6EA3BA]',
            ],
            [
                'label' => 'Create Request',
                'route' => 'item-requests.create',
                'active' => 'item-requests.create',
                'icon' => 'plus',
                'permission' => 'item-request.create',
                'class' => 'rounded-xl text-[#4E5C66] hover:bg-[#E2E8E4] hover:text-[#17201B] data-current:bg-[#EAF4FB] data-current:text-[#2D9CDB] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#1D2B33] dark:data-current:text-[#6EA3BA]',
            ],
            [
                'label' => 'Validation Inbox',
                'route' => 'item-requests.validation-inbox',
                'active' => 'item-requests.validation-inbox',
                'icon' => 'inbox',
                'permission' => 'item-request.validate',
                'class' => 'rounded-xl text-[#4E5C66] hover:bg-[#E2E8E4] hover:text-[#17201B] data-current:bg-[#ECF8F1] data-current:text-[#0E9F6E] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#173126] dark:data-current:text-[#75B892]',
            ],
            [
                'label' => 'Accounting Inbox',
                'route' => 'item-requests.accounting-inbox',
                'active' => 'item-requests.accounting-inbox',
                'icon' => 'banknotes',
                'permission' => 'item-request.account',
                'class' => 'rounded-xl text-[#4E5C66] hover:bg-[#E2E8E4] hover:text-[#17201B] data-current:bg-[#EAF4FB] data-current:text-[#3B82F6] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#1B2A3F] dark:data-current:text-[#7FB0FF]',
            ],
            [
                'label' => 'Export',
                'route' => 'item-requests.exports.index',
                'active' => 'item-requests.exports.*',
                'icon' => 'arrow-down-tray',
                'permission' => 'item-request.export',
                'class' => 'rounded-xl text-[#4E5C66] hover:bg-[#E2E8E4] hover:text-[#17201B] data-current:bg-[#EAF4FB] data-current:text-[#2D9CDB] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#1D2B33] dark:data-current:text-[#6EA3BA]',
            ],
        ],
    ],
    [
        'heading' => 'Administration',
        'visibility' => ['user.view', 'user.manage'],
        'class' => 'mt-3 [&_h3]:text-[#66736D] dark:[&_h3]:text-[#9BAAB3]',
        'items' => [
            [
                'label' => 'Users',
                'route' => 'admin.users.index',
                'active' => 'admin.users.*',
                'icon' => 'users',
                'permission' => 'user.view',
                'class' => 'rounded-xl text-[#4E5C66] hover:bg-[#EEF2F0] hover:text-[#17201B] data-current:bg-[#EEF2F0] data-current:text-[#4E5C66] dark:text-[#AAB4AF] dark:hover:bg-[#222927] dark:hover:text-[#E6E6E4] dark:data-current:bg-[#222927] dark:data-current:text-[#E6E6E4]',
            ],
        ],
    ],
];
