<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-gray-900">Notifyx</h1>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Dashboard
                            </a>
                            <a href="{{ route('notifications.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('notifications.index') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Notifications
                            </a>
                            <a href="{{ route('notifications.preferences') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('notifications.preferences') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Preferences
                            </a>
                        </div>
                    </div>

                    <!-- Notification Bell -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <livewire:notification-bell />
                            <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 ml-2">Logout</button>
                            </form>
                        @else
                            <a href="/login" class="text-gray-500 hover:text-gray-700">Login</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    
    <!-- Laravel Echo (for real-time notifications) -->
    <script>
        // Simulated Echo for demo - replace with actual Echo configuration
        window.Echo = {
            socketId: function() {
                return 'fake-socket-id-' + Math.random().toString(36).substr(2, 9);
            },
            private: function(channel) {
                console.log('Echo private channel:', channel);
                return {
                    listen: function(event, callback) {
                        console.log('Would listen for ' + event + ' on ' + channel);
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
