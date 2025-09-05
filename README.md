# 🔔 Notifyx - Laravel Notification System

A simple and powerful notification system for Laravel applications. Send notifications to users and display them beautifully with real-time updates.

> **📋 This is a Demo Project**
> 
> This repository demonstrates the usage of the `saurabhpunia/notifyx` package. The actual package is available on [Packagist](https://packagist.org/packages/saurabhpunia/notifyx).
> 
> To see this demo in action, clone this repository and follow the installation steps below.

## 📋 What Does This Package Do?

- **📧 Send Notifications**: Easily send notifications to users
- **🔔 Notification Bell**: Shows unread count with a nice dropdown
- **📱 Real-time Updates**: Notifications appear instantly without page refresh
- **⚙️ User Preferences**: Let users choose how they want to receive notifications
- **📄 Notification History**: Full page showing all user notifications
- **🏢 Multi-tenant Ready**: Works with apps that have multiple tenants/teams

## 🎯 Quick Demo

After installation, you can send a notification like this:

```php
// Send a simple notification
notify($user)
    ->title('Welcome!')
    ->with('Thanks for joining our platform')
    ->type('message')
    ->send();
```

And display notifications in your layout:

```blade
<!-- Add this to your layout -->
<livewire:notification-bell />
```

## 📦 Installation

### Step 1: Install the Package

```bash
composer require saurabhpunia/notifyx
```

### Step 2: Publish and Run Migrations

```bash
php artisan vendor:publish --tag="notifyx-migrations"
php artisan migrate
```

### Step 3: Publish Configuration

```bash
php artisan vendor:publish --tag="notifyx-config"
```

## 🏃‍♂️ Running This Demo

If you've cloned this demo repository:

1. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

2. **Setup Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

4. **Start the Development Server**:
   ```bash
   php artisan serve
   npm run dev
   ```

5. **Visit the Demo**: Open http://localhost:8000 in your browser

### Step 4: Add Trait to User Model

Open your `app/Models/User.php` file and add the trait:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Notifyx\Concerns\HasNotifyx;

class User extends Authenticatable
{
    use HasNotifyx; // Add this line
    
    // ... rest of your User model
}
```

### Step 5: Add to Your Layout

Add the notification bell to your main layout file:

```blade
<!-- In your layout file (e.g., resources/views/layouts/app.blade.php) -->
<div class="header">
    <!-- Your existing header content -->
    
    <!-- Add the notification bell -->
    <livewire:notification-bell />
</div>
```

## ⚙️ Configuration

### Basic Setup

The package comes with sensible defaults. You can customize notification types in `config/notifyx.php`:

```php
'types' => [
    'message' => [
        'label' => 'Messages',
        'icon' => 'heroicon-o-chat-bubble-left-right',
        'color' => 'blue'
    ],
    'system' => [
        'label' => 'System Updates',
        'icon' => 'heroicon-o-cog-6-tooth',
        'color' => 'gray'
    ],
    'billing' => [
        'label' => 'Billing',
        'icon' => 'heroicon-o-currency-dollar',
        'color' => 'green'
    ],
],
```

### Multi-tenant Applications

If your app has multiple tenants/teams, enable multi-tenancy:

```php
// config/notifyx.php

'multitenant' => true,
'tenant_resolver' => fn() => auth()->user()->current_team_id,
```

## 🚀 How to Use

### Sending Notifications

#### Simple Notification

```php
notify($user)
    ->with('Your order has been shipped!')
    ->send();
```

#### Notification with Title

```php
notify($user)
    ->title('Order Update')
    ->with('Your order #12345 has been shipped and will arrive tomorrow.')
    ->send();
```

#### Notification with Type

```php
notify($user)
    ->title('Payment Received')
    ->with('We received your payment of $99.99')
    ->type('billing')
    ->send();
```

#### Notification with Action Button

```php
notify($user)
    ->title('New Message')
    ->with('You have a new message from John')
    ->type('message')
    ->action('/messages', 'View Message')
    ->send();
```

#### Notification via Multiple Channels

```php
notify($user)
    ->title('Welcome!')
    ->with('Thanks for joining our platform')
    ->type('message')
    ->via(['database', 'mail', 'broadcast']) // Send via multiple channels
    ->send();
```

### Using the Facade

You can also use the Notifyx facade:

```php
use Notifyx\Facades\Notifyx;

Notifyx::to($user)
    ->title('Hello!')
    ->with('This is a test notification')
    ->send();
```

### Display Components

#### Notification Bell (with dropdown)

```blade
<livewire:notification-bell />
```

This shows a bell icon with unread count and dropdown with recent notifications.

#### Full Notification Page

Create a route and view for the full notification page:

```php
// routes/web.php
Route::get('/notifications', function() {
    return view('notifications');
})->name('notifications.index');
```

```blade
<!-- resources/views/notifications.blade.php -->
@extends('layouts.app')

@section('content')
    <livewire:notification-page />
@endsection
```

#### User Preferences Page

Let users customize their notification preferences:

```blade
<!-- resources/views/notification-preferences.blade.php -->
@extends('layouts.app')

@section('content')
    <livewire:notification-preferences />
@endsection
```

## 📱 Frontend Features

### Real-time Updates

If you have Laravel Echo setup for broadcasting, notifications will appear in real-time without page refresh.

### SPA-like Experience

All interactions (marking as read, deleting, etc.) happen without page reloads using Livewire.

### User Preferences

Users can control:
- Which notification types they want to receive
- Which channels (email, browser, etc.) to use for each type
- Enable/disable notifications entirely

## 🎨 Customization

### Custom Views

Publish the views to customize the appearance:

```bash
php artisan vendor:publish --tag="notifyx-views"
```

Views will be published to `resources/views/vendor/notifyx/`.

### Custom Notification Types

Add your own notification types in the config:

```php
'types' => [
    'order' => [
        'label' => 'Order Updates',
        'icon' => 'heroicon-o-shopping-bag',
        'color' => 'purple'
    ],
    'friend_request' => [
        'label' => 'Friend Requests',
        'icon' => 'heroicon-o-user-plus',
        'color' => 'pink'
    ],
],
```

## 🔧 Advanced Usage

### Get User Notifications

```php
// Get recent notifications
$notifications = $user->getNotifications(10);

// Get paginated notifications
$notifications = $user->getPaginatedNotifications(15);

// Get unread count
$unreadCount = $user->getUnreadCount();

// Mark notification as read
$user->markNotificationAsRead($notificationId);

// Mark all as read
$user->markAllNotificationsAsRead();
```

### Check User Preferences

```php
// Check if user has enabled email notifications for billing
if ($user->hasEnabledNotification('billing', 'mail')) {
    // Send billing notification via email
}
```

### Testing

You can fake notifications in tests:

```php
use Notifyx\Facades\Notifyx;

// In your test
Notifyx::fake();

// Your code that sends notifications
notify($user)->with('Test message')->send();

// Assert notification was sent
Notifyx::assertSentTo($user, function ($notification) {
    return $notification->message === 'Test message';
});
```

## 📊 Database Tables

The package creates these tables:

- `notifications` - Stores all notifications (Laravel's default)
- `notification_preferences` - Stores user preferences for notification types/channels

## 🔍 Troubleshooting

### Notifications Not Appearing

1. **Check if the trait is added** to your User model
2. **Make sure migrations ran** with `php artisan migrate`
3. **Clear cache** with `php artisan config:clear`

### Real-time Not Working

1. **Setup Laravel Echo** for broadcasting
2. **Configure broadcasting driver** (Pusher, Redis, etc.)
3. **Check broadcast channel permissions**

### Styling Issues

1. **Make sure Tailwind CSS is included** in your project
2. **Publish views** and customize if needed
3. **Check for CSS conflicts** with your existing styles

## 📝 Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 10.0, 11.0, or 12.0
- **Livewire**: 3.0 or higher

## 🤝 Support

For issues or questions:

1. Check the troubleshooting section above
2. Look at the test files in `/testing/` directory for examples
3. Create an issue if you find a bug

## 📄 License

This package is open-source software licensed under the MIT license.

---

## 🎉 That's It!

You now have a complete notification system in your Laravel app. Users can receive notifications, view them in a beautiful interface, and customize their preferences. The system works great for both simple websites and complex multi-tenant applications.

Happy coding! 🚀
