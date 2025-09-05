<div class="w-full">
    <!-- Header -->
    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
            <div class="flex items-center space-x-2">
                @if(count($notifications) > 0)
                    <button wire:click="markAllAsRead" 
                            class="text-xs text-indigo-600 hover:text-indigo-500 font-medium">
                        Mark all read
                    </button>
                @endif
                <button wire:click="viewAll" 
                        class="text-xs text-gray-500 hover:text-gray-700 font-medium">
                    View all
                </button>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-h-96 overflow-y-auto">
        @if($loading)
            <!-- Loading State -->
            <div class="px-4 py-8 text-center">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600 mx-auto"></div>
                <p class="text-sm text-gray-500 mt-2">Loading...</p>
            </div>
        @elseif(count($notifications) > 0)
            <!-- Notifications List -->
            <div class="divide-y divide-gray-100">
                @foreach($notifications as $notification)
                    <div class="px-4 py-3 hover:bg-gray-50 transition-colors duration-150 cursor-pointer"
                         wire:click="visitNotification('{{ $notification['id'] }}', '{{ $notification['action_url'] }}')">
                        <div class="flex items-start space-x-3">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-{{ $notification['color'] }}-100 rounded-full flex items-center justify-center">
                                    @if($notification['icon'])
                                        <svg class="w-4 h-4 text-{{ $notification['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-{{ $notification['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                @if($notification['title'])
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $notification['title'] }}
                                    </p>
                                @endif
                                <p class="text-sm text-gray-600 {{ $notification['title'] ? 'mt-1' : '' }}">
                                    {{ $notification['message'] }}
                                </p>
                                <div class="flex items-center justify-between mt-2">
                                    <p class="text-xs text-gray-400">{{ $notification['created_at'] }}</p>
                                    @if($notification['action_url'])
                                        <span class="text-xs text-indigo-600 font-medium">
                                            {{ $notification['action_text'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Mark as Read Button -->
                            <div class="flex-shrink-0">
                                <button wire:click.stop="markAsRead('{{ $notification['id'] }}')"
                                        class="text-gray-400 hover:text-gray-600 transition-colors duration-150"
                                        title="Mark as read">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="px-4 py-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    @if(count($notifications) > 0)
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 rounded-b-lg">
            <a href="{{ route('notifications.index') }}" 
               class="block text-center text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                View all notifications
            </a>
        </div>
    @endif
</div>
