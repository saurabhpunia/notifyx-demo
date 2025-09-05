<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Channels
    |--------------------------------------------------------------------------
    |
    | Available channels for sending notifications. You can add or remove
    | channels based on your application needs.
    |
    */
    'channels' => ['database', 'broadcast', 'mail'],

    /*
    |--------------------------------------------------------------------------
    | Notification Types
    |--------------------------------------------------------------------------
    |
    | Define different types of notifications with their labels and icons.
    | Icons should be compatible with your frontend icon library.
    |
    */
    'types' => [
        'message' => [
            'label' => 'Messages',
            'icon' => 'heroicon-o-chat-bubble-left-right',
            'color' => 'blue'
        ],
        'system' => [
            'label' => 'System',
            'icon' => 'heroicon-o-cog-6-tooth',
            'color' => 'gray'
        ],
        'billing' => [
            'label' => 'Billing',
            'icon' => 'heroicon-o-currency-dollar',
            'color' => 'green'
        ],
        'alert' => [
            'label' => 'Alerts',
            'icon' => 'heroicon-o-exclamation-triangle',
            'color' => 'red'
        ],
        'info' => [
            'label' => 'Information',
            'icon' => 'heroicon-o-information-circle',
            'color' => 'blue'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Expiry
    |--------------------------------------------------------------------------
    |
    | Number of days after which notifications will be considered old.
    | Set to null to disable expiry.
    |
    */
    'expiry_days' => 60,

    /*
    |--------------------------------------------------------------------------
    | User Preferences
    |--------------------------------------------------------------------------
    |
    | Enable or disable user notification preferences functionality.
    |
    */
    'preferences' => true,

    /*
    |--------------------------------------------------------------------------
    | Multitenancy Support
    |--------------------------------------------------------------------------
    |
    | Enable multitenancy support for notifications. When enabled, notifications
    | will be scoped to the current tenant.
    |
    */
    'multitenant' => false,

    /*
    |--------------------------------------------------------------------------
    | Tenant Resolver
    |--------------------------------------------------------------------------
    |
    | Closure to resolve the current tenant. This will be called when
    | multitenancy is enabled to get the current tenant ID.
    |
    | Examples:
    | - For Spatie Laravel Multitenancy: fn() => tenant()?->id
    | - For Stancl Tenancy: fn() => tenant('id')
    | - For custom tenant resolution: fn() => auth()->user()?->current_team_id
    |
    */
    'tenant_resolver' => null,
    // Example implementations:
    // 'tenant_resolver' => fn() => tenant()?->id,
    // 'tenant_resolver' => fn() => auth()->user()?->current_team_id,
    // 'tenant_resolver' => fn() => session('current_tenant_id'),

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Number of notifications to display per page in the notification center.
    |
    */
    'per_page' => 15,

    /*
    |--------------------------------------------------------------------------
    | Real-time Broadcasting
    |--------------------------------------------------------------------------
    |
    | Configuration for real-time notification broadcasting.
    |
    */
    'broadcasting' => [
        'enabled' => true,
        'channel_name' => 'notifications',
        'event_name' => 'NotificationPushed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for database notifications.
    |
    */
    'database' => [
        'table' => 'notifications',
        'preferences_table' => 'notification_preferences',
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the frontend components.
    |
    */
    'ui' => [
        'theme' => 'default', // default, dark
        'max_dropdown_items' => 5,
        'show_timestamps' => true,
        'show_avatars' => false,
        'auto_mark_read' => true, // Auto mark as read when clicked
    ],
];
