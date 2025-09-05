<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Notifyx - Multi-tenancy Testing\n";
echo "====================================================\n\n";

// Test 1: Single Tenant Mode (Default)
echo "📋 TEST 1: Single Tenant Mode\n";
echo "------------------------------\n";

// Ensure multitenant is disabled
config(['notifyx.multitenant' => false]);

// Create test users
$user1 = App\Models\User::firstOrCreate(
    ['email' => 'user1@example.com'],
    ['name' => 'User One', 'password' => bcrypt('password'), 'email_verified_at' => now()]
);

$user2 = App\Models\User::firstOrCreate(
    ['email' => 'user2@example.com'],
    ['name' => 'User Two', 'password' => bcrypt('password'), 'email_verified_at' => now()]
);

echo "✅ Created users: {$user1->name} and {$user2->name}\n";

// Send notifications to both users
notify($user1)
    ->title('Single Tenant - User 1')
    ->with('This is a notification for User 1 in single tenant mode')
    ->type('message')
    ->via(['database'])
    ->send();

notify($user2)
    ->title('Single Tenant - User 2')
    ->with('This is a notification for User 2 in single tenant mode')
    ->type('system')
    ->via(['database'])
    ->send();

echo "📧 Sent notifications to both users\n";
echo "📊 User 1 unread count: " . $user1->getUnreadCount() . "\n";
echo "📊 User 2 unread count: " . $user2->getUnreadCount() . "\n";
echo "🗄️ Total notifications in DB: " . \DB::table('notifications')->count() . "\n\n";

// Test 2: Multi-Tenant Mode
echo "📋 TEST 2: Multi-Tenant Mode\n";
echo "-----------------------------\n";

// Enable multitenancy with a simple resolver
config(['notifyx.multitenant' => true]);

// Simulate tenant 1
$currentTenantId = 'tenant_1';
config(['notifyx.tenant_resolver' => fn() => $currentTenantId]);

echo "🏢 Current tenant: {$currentTenantId}\n";

// Send notifications in tenant 1
notify($user1)
    ->title('Multi-Tenant - Tenant 1 - User 1')
    ->with('This is a notification for User 1 in Tenant 1')
    ->type('billing')
    ->via(['database'])
    ->send();

notify($user2)
    ->title('Multi-Tenant - Tenant 1 - User 2')
    ->with('This is a notification for User 2 in Tenant 1')
    ->type('alert')
    ->via(['database'])
    ->send();

echo "📧 Sent notifications to both users in Tenant 1\n";

// Check counts for tenant 1
echo "📊 Tenant 1 - User 1 unread count: " . $user1->getUnreadCount() . "\n";
echo "📊 Tenant 1 - User 2 unread count: " . $user2->getUnreadCount() . "\n";

// Switch to tenant 2
$currentTenantId = 'tenant_2';
echo "🏢 Switched to tenant: {$currentTenantId}\n";

// Send notifications in tenant 2
notify($user1)
    ->title('Multi-Tenant - Tenant 2 - User 1')
    ->with('This is a notification for User 1 in Tenant 2')
    ->type('info')
    ->via(['database'])
    ->send();

echo "📧 Sent notification to User 1 in Tenant 2\n";

// Check counts for tenant 2
echo "📊 Tenant 2 - User 1 unread count: " . $user1->getUnreadCount() . "\n";
echo "📊 Tenant 2 - User 2 unread count: " . $user2->getUnreadCount() . "\n";

// Test 3: Tenant Isolation
echo "\n📋 TEST 3: Tenant Isolation\n";
echo "----------------------------\n";

// Switch back to tenant 1
$currentTenantId = 'tenant_1';
echo "🏢 Back to tenant: {$currentTenantId}\n";
echo "📊 Tenant 1 - User 1 unread count: " . $user1->getUnreadCount() . "\n";
echo "📊 Tenant 1 - User 2 unread count: " . $user2->getUnreadCount() . "\n";

// Switch to tenant 2
$currentTenantId = 'tenant_2';
echo "🏢 Back to tenant: {$currentTenantId}\n";
echo "📊 Tenant 2 - User 1 unread count: " . $user1->getUnreadCount() . "\n";
echo "📊 Tenant 2 - User 2 unread count: " . $user2->getUnreadCount() . "\n";

// Test 4: Notification Preferences with Multitenancy
echo "\n📋 TEST 4: Notification Preferences with Multitenancy\n";
echo "------------------------------------------------------\n";

// Create preferences for tenant 1
$currentTenantId = 'tenant_1';
echo "🏢 Setting preferences for tenant: {$currentTenantId}\n";

// Disable database notifications for user 1 in tenant 1
Notifyx\Models\NotificationPreference::setPreference(
    $user1->id, 
    'message', 
    'database', 
    false
);

echo "🚫 Disabled 'message' database notifications for User 1 in Tenant 1\n";

// Try to send a message notification to user 1 in tenant 1 (should be blocked)
notify($user1)
    ->title('Should be blocked')
    ->with('This notification should be blocked by preferences')
    ->type('message')
    ->via(['database'])
    ->send();

echo "📧 Attempted to send blocked notification\n";
echo "📊 Tenant 1 - User 1 unread count: " . $user1->getUnreadCount() . " (should not increase)\n";

// Switch to tenant 2 and send same type (should work)
$currentTenantId = 'tenant_2';
echo "🏢 Switched to tenant: {$currentTenantId}\n";

notify($user1)
    ->title('Should work in Tenant 2')
    ->with('This notification should work in Tenant 2')
    ->type('message')
    ->via(['database'])
    ->send();

echo "📧 Sent notification in Tenant 2\n";
echo "📊 Tenant 2 - User 1 unread count: " . $user1->getUnreadCount() . " (should increase)\n";

// Test 5: Database verification
echo "\n📋 TEST 5: Database Verification\n";
echo "---------------------------------\n";

$totalNotifications = \DB::table('notifications')->count();
$tenant1Notifications = \DB::table('notifications')->where('data->tenant_id', 'tenant_1')->count();
$tenant2Notifications = \DB::table('notifications')->where('data->tenant_id', 'tenant_2')->count();
$nullTenantNotifications = \DB::table('notifications')->whereNull('data->tenant_id')->count();

echo "🗄️ Total notifications: {$totalNotifications}\n";
echo "🏢 Tenant 1 notifications: {$tenant1Notifications}\n";
echo "🏢 Tenant 2 notifications: {$tenant2Notifications}\n";
echo "🏢 Single-tenant notifications: {$nullTenantNotifications}\n";

$totalPreferences = \DB::table('notification_preferences')->count();
$tenant1Preferences = \DB::table('notification_preferences')->where('tenant_id', 'tenant_1')->count();
$tenant2Preferences = \DB::table('notification_preferences')->where('tenant_id', 'tenant_2')->count();
$nullTenantPreferences = \DB::table('notification_preferences')->whereNull('tenant_id')->count();

echo "⚙️ Total preferences: {$totalPreferences}\n";
echo "🏢 Tenant 1 preferences: {$tenant1Preferences}\n";
echo "🏢 Tenant 2 preferences: {$tenant2Preferences}\n";
echo "🏢 Single-tenant preferences: {$nullTenantPreferences}\n";

echo "\n✅ Multi-tenancy testing completed!\n";
echo "===================================\n\n";

// Reset to single tenant for demo
config(['notifyx.multitenant' => false]);
echo "🔄 Reset to single-tenant mode for demo\n";
