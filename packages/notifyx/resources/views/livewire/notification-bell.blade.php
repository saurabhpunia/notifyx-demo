<div x-data="{ 
        show: @js($showDropdown),
        toggle() {
            this.show = !this.show;
            if (this.show) {
                $wire.call('toggleDropdown');
            } else {
                $wire.call('closeDropdown');
            }
        },
        close() {
            this.show = false;
            $wire.call('closeDropdown');
        }
     }" 
     @click.away="close()"
     class="relative">
    
    <!-- Bell Button -->
    <button 
        @click="toggle()"
        type="button"
        class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full transition-colors duration-200"
        :class="{ 'text-indigo-600': show }">
        
        <!-- Bell Icon -->
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        
        <!-- Unread Count Badge -->
        @if($this->unreadCount > 0)
            <span class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center min-w-[1.25rem]">
                {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div x-show="show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50">
        
        @if(auth()->check())
            @livewire('notification-dropdown')
        @else
            <div class="p-4 text-center text-gray-500">
                Please log in to view notifications
            </div>
        @endif
    </div>
</div>

@script
<script>
    // Handle notification sound or other effects
    $wire.on('bell-notification-received', (notification) => {
        // Optional: Play notification sound
        // new Audio('/notification-sound.mp3').play();
        
        // Optional: Show browser notification
        if (Notification.permission === 'granted') {
            new Notification(notification.title || 'New Notification', {
                body: notification.message,
                icon: '/favicon.ico'
            });
        }
    });
</script>
@endscript
