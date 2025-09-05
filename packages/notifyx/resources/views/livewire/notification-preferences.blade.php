<div>
    <!-- Page Header -->
    <div class="bg-white shadow">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Notification Preferences
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage how you receive notifications for different types of events.
                    </p>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    <button wire:click="resetToDefaults"
                            onclick="confirm('Are you sure you want to reset all preferences to defaults?') || event.stopImmediatePropagation()"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Reset to Defaults
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Preferences -->
    <div class="px-4 sm:px-6 lg:px-8 py-6">
        @if($loading)
            <div class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                <span class="ml-2 text-sm text-gray-500">Loading preferences...</span>
            </div>
        @else
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <!-- Channel Headers -->
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-{{ count($channels) + 1 }}">
                        <div class="font-medium text-gray-900">Notification Type</div>
                        @foreach($channels as $channel)
                            <div class="text-center">
                                <div class="font-medium text-gray-900">{{ $channelLabels[$channel] ?? ucfirst($channel) }}</div>
                                <div class="mt-2 flex justify-center space-x-2">
                                    <button wire:click="enableAllForChannel('{{ $channel }}')"
                                            class="text-xs text-green-600 hover:text-green-500 font-medium">
                                        Enable all
                                    </button>
                                    <span class="text-gray-300">|</span>
                                    <button wire:click="disableAllForChannel('{{ $channel }}')"
                                            class="text-xs text-red-600 hover:text-red-500 font-medium">
                                        Disable all
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Preference Rows -->
                <div class="divide-y divide-gray-200">
                    @foreach($preferences as $type => $typeConfig)
                        <div class="px-4 py-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-{{ count($channels) + 1 }} items-center">
                                <!-- Type Info -->
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-{{ $typeConfig['color'] }}-100 rounded-lg flex items-center justify-center">
                                            @if($typeConfig['icon'])
                                                <svg class="w-4 h-4 text-{{ $typeConfig['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-{{ $typeConfig['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $typeConfig['label'] }}</div>
                                        <div class="flex space-x-2 mt-1">
                                            <button wire:click="enableAllForType('{{ $type }}')"
                                                    class="text-xs text-green-600 hover:text-green-500 font-medium">
                                                Enable all
                                            </button>
                                            <span class="text-gray-300">|</span>
                                            <button wire:click="disableAllForType('{{ $type }}')"
                                                    class="text-xs text-red-600 hover:text-red-500 font-medium">
                                                Disable all
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Channel Toggles -->
                                @foreach($channels as $channel)
                                    <div class="flex justify-center">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox"
                                                   wire:change="updatePreference('{{ $type }}', '{{ $channel }}', $event.target.checked)"
                                                   {{ $typeConfig['channels'][$channel]['enabled'] ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">About notification channels</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($channels as $channel)
                                    <li>
                                        <strong>{{ $channelLabels[$channel] ?? ucfirst($channel) }}:</strong>
                                        @switch($channel)
                                            @case('database')
                                                Notifications appear in your in-app notification center
                                                @break
                                            @case('mail')
                                                Notifications are sent to your email address
                                                @break
                                            @case('broadcast')
                                                Real-time notifications appear instantly when the page is open
                                                @break
                                            @case('sms')
                                                Notifications are sent via SMS to your phone number
                                                @break
                                            @case('slack')
                                                Notifications are sent to your connected Slack workspace
                                                @break
                                            @default
                                                {{ ucfirst($channel) }} notifications
                                        @endswitch
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@script
<script>
    $wire.on('preference-updated', (data) => {
        // Optional: Show toast notification
        console.log('Preference updated:', data);
    });

    $wire.on('preferences-reset', () => {
        // Optional: Show success message
        console.log('Preferences reset to defaults');
    });
</script>
@endscript
