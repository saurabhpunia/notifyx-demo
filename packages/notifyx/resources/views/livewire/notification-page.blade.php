<div>
    <!-- Page Header -->
    <div class="bg-white shadow">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Notifications
                    </h1>
                    @if($unreadCount > 0)
                        <p class="mt-1 text-sm text-gray-500">
                            You have {{ $unreadCount }} unread notification{{ $unreadCount === 1 ? '' : 's' }}
                        </p>
                    @endif
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    @if($unreadCount > 0)
                        <button wire:click="markAllAsRead"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Mark all as read
                        </button>
                    @endif
                    <a href="{{ route('notifications.preferences') }}"
                       wire:navigate
                       class="ml-3 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Preferences
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input wire:model.live.debounce.300ms="search" 
                           type="text" 
                           id="search"
                           placeholder="Search notifications..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <!-- Type Filter -->
                <div>
                    <label for="type-filter" class="block text-sm font-medium text-gray-700">Type</label>
                    <select wire:model.live="typeFilter" 
                            id="type-filter"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">All types</option>
                        @foreach($notificationTypes as $type => $config)
                            <option value="{{ $type }}">{{ $config['label'] ?? ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Read Status Filter -->
                <div>
                    <label for="read-status-filter" class="block text-sm font-medium text-gray-700">Status</label>
                    <select wire:model.live="readStatusFilter" 
                            id="read-status-filter"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">All</option>
                        <option value="unread">Unread</option>
                        <option value="read">Read</option>
                    </select>
                </div>

                <!-- Clear Filters -->
                <div class="flex items-end">
                    <button wire:click="clearFilters"
                            class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Clear filters
                    </button>
                </div>
            </div>

            <!-- Date Range -->
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="date-from" class="block text-sm font-medium text-gray-700">From Date</label>
                    <input wire:model.live="dateFrom" 
                           type="date" 
                           id="date-from"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="date-to" class="block text-sm font-medium text-gray-700">To Date</label>
                    <input wire:model.live="dateTo" 
                           type="date" 
                           id="date-to"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="px-4 sm:px-6 lg:px-8 py-6">
        @if($notifications->count() > 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @foreach($notifications as $notification)
                        @php
                            $data = $notification->data;
                            $typeConfig = config("notifyx.types.{$notification->type_tag}", []);
                            $isRead = $notification->read_at !== null;
                        @endphp
                        
                        <li class="{{ $isRead ? 'bg-white' : 'bg-blue-50' }}">
                            <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors duration-150">
                                <div class="flex items-center space-x-4 flex-1">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-{{ $typeConfig['color'] ?? 'gray' }}-100 rounded-full flex items-center justify-center">
                                            @if($typeConfig['icon'] ?? null)
                                                <svg class="w-5 h-5 text-{{ $typeConfig['color'] ?? 'gray' }}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-{{ $typeConfig['color'] ?? 'gray' }}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                @if($data['title'] ?? null)
                                                    <p class="text-sm font-medium text-gray-900 {{ $isRead ? '' : 'font-semibold' }}">
                                                        {{ $data['title'] }}
                                                    </p>
                                                @endif
                                                <p class="text-sm text-gray-600 {{ ($data['title'] ?? null) ? 'mt-1' : '' }}">
                                                    {{ $data['message'] ?? '' }}
                                                </p>
                                                <div class="flex items-center mt-2 space-x-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $typeConfig['color'] ?? 'gray' }}-100 text-{{ $typeConfig['color'] ?? 'gray' }}-800">
                                                        {{ $typeConfig['label'] ?? ucfirst($notification->type_tag ?? 'notification') }}
                                                    </span>
                                                    <time class="text-sm text-gray-500">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </time>
                                                    @if($isRead)
                                                        <span class="inline-flex items-center text-xs text-green-600">
                                                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                            </svg>
                                                            Read
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    @if($data['action_url'] ?? null)
                                        <a href="{{ $data['action_url'] }}" 
                                           onclick="@if(!$isRead) $wire.markAsRead('{{ $notification->id }}') @endif"
                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            {{ $data['action_text'] ?? 'View' }}
                                        </a>
                                    @endif
                                    
                                    @if(!$isRead)
                                        <button wire:click="markAsRead('{{ $notification->id }}')"
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Mark read
                                        </button>
                                    @endif
                                    
                                    <button wire:click="deleteNotification('{{ $notification->id }}')"
                                            onclick="confirm('Are you sure you want to delete this notification?') || event.stopImmediatePropagation()"
                                            class="text-gray-400 hover:text-red-500 transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search || $typeFilter || $readStatusFilter || $dateFrom || $dateTo)
                        No notifications match your current filters.
                    @else
                        You don't have any notifications yet.
                    @endif
                </p>
                @if($search || $typeFilter || $readStatusFilter || $dateFrom || $dateTo)
                    <div class="mt-6">
                        <button wire:click="clearFilters"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Clear filters
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
