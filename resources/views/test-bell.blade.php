<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Notification Bell</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Test Notification Bell</h1>
        
        @auth
            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <h2 class="text-xl font-semibold mb-4">User Info</h2>
                <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
                <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                <p><strong>Unread Count:</strong> {{ auth()->user()->getUnreadCount() }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <h2 class="text-xl font-semibold mb-4">Notification Bell Component</h2>
                <div class="flex justify-center">
                    @livewire('notification-bell')
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Recent Notifications</h2>
                @php
                    $notifications = auth()->user()->notifications()->latest()->take(5)->get();
                @endphp
                
                @if($notifications->count() > 0)
                    <div class="space-y-2">
                        @foreach($notifications as $notification)
                            <div class="p-3 border rounded {{ $notification->read_at ? 'bg-gray-50' : 'bg-blue-50' }}">
                                <div class="font-medium">{{ $notification->data['title'] ?? 'No Title' }}</div>
                                <div class="text-sm text-gray-600">{{ $notification->data['message'] ?? 'No Message' }}</div>
                                <div class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No notifications found.</p>
                @endif
            </div>
        @else
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                Not authenticated. Please log in first.
            </div>
        @endauth
    </div>

    @livewireScripts
    
    <!-- Laravel Echo simulation -->
    <script>
        window.Echo = {
            socketId: function() {
                return 'fake-socket-id-' + Math.random().toString(36).substr(2, 9);
            },
            private: function(channel) {
                console.log('Echo private channel:', channel);
                return {
                    listen: function(event, callback) {
                        console.log('Echo listen:', event);
                        return this;
                    },
                    listenForWhisper: function(event, callback) {
                        console.log('Would listen for whisper ' + event + ' on ' + channel);
                        return this;
                    }
                };
            },
            channel: function(channel) {
                console.log('Echo public channel:', channel);
                return {
                    listen: function(event, callback) {
                        console.log('Would listen for ' + event + ' on ' + channel);
                        return this;
                    }
                };
            },
            join: function(channel) {
                console.log('Echo join channel:', channel);
                return {
                    here: function(callback) {
                        console.log('Would get here users for ' + channel);
                        return this;
                    },
                    joining: function(callback) {
                        console.log('Would listen for joining users on ' + channel);
                        return this;
                    },
                    leaving: function(callback) {
                        console.log('Would listen for leaving users on ' + channel);
                        return this;
                    },
                    listen: function(event, callback) {
                        console.log('Would listen for ' + event + ' on ' + channel);
                        return this;
                    },
                    listenForWhisper: function(event, callback) {
                        console.log('Would listen for whisper ' + event + ' on ' + channel);
                        return this;
                    }
                };
            }
        };
    </script>
</body>
</html>
