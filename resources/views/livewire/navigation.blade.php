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
                    <button 
                        wire:click="goToDashboard" 
                        class="inline-flex items-center px-1 pt-1 border-b-2 {{ $currentRoute === 'dashboard' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                        Dashboard
                    </button>
                    <button 
                        wire:click="goToNotifications" 
                        class="inline-flex items-center px-1 pt-1 border-b-2 {{ str_contains($currentRoute, 'notifications.index') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                        Notifications
                    </button>
                    <button 
                        wire:click="goToPreferences" 
                        class="inline-flex items-center px-1 pt-1 border-b-2 {{ str_contains($currentRoute, 'notifications.preferences') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                        Preferences
                    </button>
                </div>
            </div>

            <!-- Notification Bell -->
            <div class="flex items-center space-x-4">
                @auth
                    <livewire:notification-bell />
                    <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
                    <button 
                        wire:click="logout" 
                        wire:loading.attr="disabled"
                        class="text-sm text-gray-500 hover:text-gray-700 ml-2 disabled:opacity-50">
                        <span wire:loading.remove>Logout</span>
                        <span wire:loading>Logging out...</span>
                    </button>
                @else
                    <button 
                        wire:click="goToLogin" 
                        class="text-gray-500 hover:text-gray-700">
                        Login
                    </button>
                @endauth
            </div>
        </div>
    </div>
</nav>
