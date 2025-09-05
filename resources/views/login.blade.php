<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Notifyx Demo</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Notifyx Demo
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Click the button below to log in as a demo user
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-8">
                <form method="POST" action="/login">
                    @csrf
                    <div class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Demo Login</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>This will create and log you in as a demo user (demo@example.com). You can then test all the notification features!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            Login as Demo User
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">What you'll be able to test:</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Send different types of notifications</li>
                        <li>• View notifications in the bell dropdown</li>
                        <li>• Browse notification history</li>
                        <li>• Manage notification preferences</li>
                        <li>• See real-time updates (simulated)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
