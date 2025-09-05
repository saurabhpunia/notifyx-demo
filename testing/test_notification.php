<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create or get demo user
$user = App\Models\User::firstOrCreate(
    ['email' => 'demo@example.com'],
    [
        'name' => 'Demo User',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]
);

echo "User created/found: {$user->name} ({$user->email})\n";

// Test the notification system
try {
    $notifier = notify($user)
        ->title('Test Notification')
        ->with('This is a test notification from the Notifyx!')
        ->type('welcome')
        ->via(['database']);
        
    echo "🔧 Notifier created, about to send...\n";
    
    $result = $notifier->send();
    
    echo "✅ Notification sent! Result: " . var_export($result, true) . "\n";
    echo "📊 User unread count: " . $user->getUnreadCount() . "\n";
    
    // Get the latest notification
    $latestNotification = $user->notifications()->latest()->first();
    if ($latestNotification) {
        echo "📝 Latest notification: " . $latestNotification->data['title'] . "\n";
        echo "🔗 Notification ID: " . $latestNotification->id . "\n";
    } else {
        echo "❌ No notifications found for user\n";
    }
    
    // Check database directly
    echo "🗄️ Total notifications in DB: " . \DB::table('notifications')->count() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error creating notification: " . $e->getMessage() . "\n";
    echo "🔍 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
