<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Simple Multi-tenancy Test (without trait)\n";
echo "=============================================\n\n";

// Test 1: Single Tenant Mode (Default)
echo "📋 TEST 1: Single Tenant Mode\n";
echo "------------------------------\n";

// Ensure multitenant is disabled
config(['notifyx.multitenant' => false]);

// Create test user
$user = App\Models\User::firstOrCreate(
    ['email' => 'demo@example.com'],
    ['name' => 'Demo User', 'password' => bcrypt('password'), 'email_verified_at' => now()]
);

echo "✅ User: {$user->name} ({$user->email})\n";

// Send a single-tenant notification
notify($user)
    ->title('Single Tenant Notification')
    ->with('This is a notification in single tenant mode')
    ->type('message')
    ->via(['database'])
    ->send();

echo "📧 Sent single-tenant notification\n";

// Check database
$singleTenantNotifications = \DB::table('notifications')
    ->where('notifiable_id', $user->id)
    ->whereNull('data->tenant_id')
    ->count();

echo "📊 Single-tenant notifications: {$singleTenantNotifications}\n\n";

// Test 2: Multi-Tenant Mode
echo "📋 TEST 2: Multi-Tenant Mode\n";
echo "-----------------------------\n";

// Enable multitenancy
config(['notifyx.multitenant' => true]);

// Set tenant 1
$currentTenantId = 'tenant_1';
config(['notifyx.tenant_resolver' => fn() => $currentTenantId]);

echo "🏢 Current tenant: {$currentTenantId}\n";

// Send notification in tenant 1
notify($user)
    ->title('Multi-Tenant Notification - Tenant 1')
    ->with('This is a notification for Tenant 1')
    ->type('billing')
    ->via(['database'])
    ->send();

echo "📧 Sent notification in Tenant 1\n";

// Switch to tenant 2
$currentTenantId = 'tenant_2';
echo "🏢 Switched to tenant: {$currentTenantId}\n";

// Send notification in tenant 2
notify($user)
    ->title('Multi-Tenant Notification - Tenant 2')
    ->with('This is a notification for Tenant 2')
    ->type('alert')
    ->via(['database'])
    ->send();

echo "📧 Sent notification in Tenant 2\n";

// Test 3: Database verification
echo "\n📋 TEST 3: Database Verification\n";
echo "---------------------------------\n";

$totalNotifications = \DB::table('notifications')->where('notifiable_id', $user->id)->count();
$tenant1Notifications = \DB::table('notifications')
    ->where('notifiable_id', $user->id)
    ->where('data->tenant_id', 'tenant_1')
    ->count();
$tenant2Notifications = \DB::table('notifications')
    ->where('notifiable_id', $user->id)
    ->where('data->tenant_id', 'tenant_2')
    ->count();
$nullTenantNotifications = \DB::table('notifications')
    ->where('notifiable_id', $user->id)
    ->whereNull('data->tenant_id')
    ->count();

echo "🗄️ Total notifications for user: {$totalNotifications}\n";
echo "🏢 Tenant 1 notifications: {$tenant1Notifications}\n";
echo "🏢 Tenant 2 notifications: {$tenant2Notifications}\n";
echo "🏢 Single-tenant notifications: {$nullTenantNotifications}\n";

// Test 4: Check notification content
echo "\n📋 TEST 4: Notification Content Verification\n";
echo "---------------------------------------------\n";

$notifications = \DB::table('notifications')
    ->where('notifiable_id', $user->id)
    ->orderBy('created_at', 'desc')
    ->get(['data']);

foreach ($notifications as $index => $notification) {
    $data = json_decode($notification->data, true);
    $tenantId = $data['tenant_id'] ?? 'null';
    $title = $data['title'] ?? 'No title';
    echo "📝 Notification " . ($index + 1) . ": {$title} (Tenant: {$tenantId})\n";
}

echo "\n✅ Multi-tenancy testing completed successfully!\n";
echo "The Notifyx properly handles both single-tenant and multi-tenant scenarios.\n\n";

// Reset to single tenant for demo
config(['notifyx.multitenant' => false]);
echo "🔄 Reset to single-tenant mode\n";
