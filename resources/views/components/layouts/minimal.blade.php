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
    {{ $slot }}

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
