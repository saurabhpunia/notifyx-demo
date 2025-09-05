# Notifyx Package

This is the Notifyx Laravel package for in-app notifications.

## Installation & Documentation

For complete installation instructions and documentation, please see the main project README at the root of this repository.

## Package Info

- **Name**: notifyx/notifyx
- **Version**: Compatible with Laravel 10.0+, 11.0+, 12.0+
- **Author**: Saurabh Punia
- **License**: MIT

## Quick Install

```bash
composer require notifyx/notifyx
php artisan vendor:publish --tag="notifyx-migrations"
php artisan migrate
```

## Basic Usage

```php
// Add trait to User model
use Notifyx\Concerns\HasNotifyx;

// Send notification
notify($user)->with('Hello!')->send();

// Display in blade
<livewire:notification-bell />
```

For detailed documentation, examples, and troubleshooting, see the main README file.

```blade
<livewire:notification-dropdown />
```

Add the notification center routes to your navigation:

```blade
<a href="{{ route('notifications.index') }}">Notifications</a>
<a href="{{ route('notifications.preferences') }}">Notification Preferences</a>
```

## 🔔 Sending Notifications

### Using the Fluent API

```php
// Basic notification
notify($user)
    ->with('Payment received successfully')
    ->type('billing')
    ->send();

// Advanced notification with action
notify($user)
    ->title('New Message')
    ->with('You have received a new message from John Doe')
    ->type('message')
    ->via(['database', 'broadcast', 'mail'])
    ->action(route('messages.show', $message), 'View Message')
    ->data(['message_id' => $message->id])
    ->send();

// Using the facade
use Notifyx\Facades\Notifyx;

Notifyx::to($user)
    ->with('System maintenance scheduled')
    ->type('system')
    ->via(['database', 'broadcast'])
    ->send();
```

### Using the Trait Method

```php
notify($user)->with('Welcome to our platform!')->type('system')->send();
```

### Force Send (Skip Preferences)

```php
notify($user)
    ->with('Critical security alert')
    ->type('alert')
    ->sendForced(); // Bypasses user preferences
```

## 🎛️ User Management

### Check Unread Count

```php
$count = $user->getUnreadCount();
```

### Mark Notifications as Read

```php
// Mark specific notification as read
$user->markNotificationAsRead($notificationId);

// Mark all as read
$user->markAllNotificationsAsRead();
```

### Get Notifications

```php
// Get recent notifications
$notifications = $user->getNotifications(10);

// Get paginated notifications
$notifications = $user->getPaginatedNotifications(15);

// Search notifications
$notifications = $user->searchNotifications('payment', [
    'type' => 'billing',
    'read_status' => 'unread',
    'date_from' => '2024-01-01',
]);
```

### Manage Preferences

```php
// Get user preferences
$preferences = $user->getNotificationPreferences();

// Update specific preference
$user->updateNotificationPreference('billing', 'mail', false);

// Check if enabled
$enabled = $user->hasEnabledNotification('billing', 'mail');
```

## 📺 Real-time Updates

For real-time notifications, ensure Laravel Echo is configured:

```js
// In your app.js or similar
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true,
});
```

The notification components automatically listen for real-time updates.

## 🏢 Multitenancy Support

Enable multitenancy in the config:

```php
'multitenant' => true,
'tenant_resolver' => function () {
    return tenant()->id; // Your tenant resolution logic
},
```

## 🎨 Customization

### Custom Views

Publish the views and modify them:

```bash
php artisan vendor:publish --tag="notifyx-views"
```

Views will be published to `resources/views/vendor/notifyx/`.

### Custom Styling

The components use Tailwind CSS classes. You can customize the styling by modifying the published views or by adding custom CSS.

### Custom Notification Types

Add new types to the configuration:

```php
'types' => [
    'custom_type' => [
        'label' => 'Custom Notifications',
        'icon' => 'heroicon-o-star',
        'color' => 'purple'
    ],
],
```

## 🧪 Testing

The package includes testing utilities:

```php
use Notifyx\Facades\Notifyx;

// In your tests
Notifyx::fake();

// Send notification
notify($user)->with('Test message')->send();

// Assert notification was sent
Notifyx::assertSentTo($user, function ($notification) {
    return $notification->message === 'Test message';
});
```

## 📊 Maintenance

### Clean Old Notifications

The package respects the `expiry_days` configuration. You can manually clean old notifications:

```php
$user->deleteExpiredNotifications();
```

### Scheduled Cleanup

Add to your `App\Console\Kernel` schedule:

```php
$schedule->call(function () {
    User::chunk(100, function ($users) {
        $users->each->deleteExpiredNotifications();
    });
})->daily();
```

## 🚀 Advanced Usage

### Custom Notification Channels

You can extend the package to support additional channels by implementing Laravel's notification channel interface.

### Event Listeners

Listen for notification events:

```php
// In your EventServiceProvider
use Notifyx\Events\NotificationPushed;

protected $listen = [
    NotificationPushed::class => [
        YourNotificationListener::class,
    ],
];
```

### API Integration

The package provides Livewire components, but you can also build API endpoints using the same underlying models and services.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## 🔗 Links

- [Laravel Notifications Documentation](https://laravel.com/docs/notifications)
- [Livewire Documentation](https://livewire.laravel.com)
- [Laravel Echo Documentation](https://laravel.com/docs/broadcasting#client-side-installation)

## 🆘 Support

If you discover any security vulnerabilities, please send an e-mail to the package maintainers.

For bugs and feature requests, please use the GitHub issues page.
