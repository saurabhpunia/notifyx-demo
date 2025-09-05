<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the demo user
$user = App\Models\User::where('email', 'demo@example.com')->first();

if (!$user) {
    echo "❌ Demo user not found\n";
    exit(1);
}

echo "🔧 Testing Notification Preferences for: {$user->name} ({$user->email})\n\n";

// Test 1: Check if method exists
if (method_exists($user, 'updateNotificationPreference')) {
    echo "✅ updateNotificationPreference method exists\n";
} else {
    echo "❌ updateNotificationPreference method missing\n";
    exit(1);
}

// Test 2: Get current preferences
echo "📊 Current notification preferences:\n";
try {
    $preferences = $user->getNotificationPreferences();
    foreach ($preferences as $type => $config) {
        echo "  - {$config['label']} ({$type}):\n";
        foreach ($config['channels'] as $channel => $channelConfig) {
            $status = $channelConfig['enabled'] ? '✅ enabled' : '❌ disabled';
            echo "    - {$channel}: {$status}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error getting preferences: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Update a preference
echo "\n🔧 Testing preference update...\n";
try {
    $user->updateNotificationPreference('message', 'mail', false);
    echo "✅ Successfully updated message/mail preference to disabled\n";
    
    // Verify the change
    $preferences = $user->getNotificationPreferences();
    $messageMailEnabled = $preferences['message']['channels']['mail']['enabled'];
    
    if (!$messageMailEnabled) {
        echo "✅ Preference update verified - message/mail is now disabled\n";
    } else {
        echo "❌ Preference update failed - message/mail is still enabled\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error updating preference: " . $e->getMessage() . "\n";
}

// Test 4: Test bulk operations
echo "\n🔧 Testing bulk operations...\n";
try {
    // Enable all mail notifications
    $channels = ['database', 'mail', 'broadcast'];
    foreach (['message', 'billing', 'system'] as $type) {
        $user->updateNotificationPreference($type, 'mail', true);
    }
    echo "✅ Enabled all mail notifications\n";
    
    // Verify
    $preferences = $user->getNotificationPreferences();
    $allMailEnabled = true;
    foreach (['message', 'billing', 'system'] as $type) {
        if (!$preferences[$type]['channels']['mail']['enabled']) {
            $allMailEnabled = false;
            break;
        }
    }
    
    if ($allMailEnabled) {
        echo "✅ Bulk mail enable verified\n";
    } else {
        echo "❌ Bulk mail enable failed\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error in bulk operations: " . $e->getMessage() . "\n";
}

// Test 5: Check database entries
echo "\n🗄️ Database verification:\n";
$dbPrefs = \DB::table('notification_preferences')->where('user_id', $user->id)->get();
echo "Found {$dbPrefs->count()} preference records in database\n";

foreach ($dbPrefs as $pref) {
    $status = $pref->is_enabled ? 'enabled' : 'disabled';
    echo "  - {$pref->type}/{$pref->channel}: {$status}\n";
}

echo "\n🎉 Notification preferences testing completed!\n";
