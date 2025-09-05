<?php

namespace Notifyx\Livewire;

use Livewire\Component;

class NotificationPreferences extends Component
{
    public array $preferences = [];
    public bool $loading = false;

    public function mount()
    {
        $this->loadPreferences();
    }

    public function loadPreferences()
    {
        $this->loading = true;
        
        if (auth()->check()) {
            $this->preferences = auth()->user()->getNotificationPreferences();
        }
        
        $this->loading = false;
    }

    public function updatePreference($type, $channel, $enabled)
    {
        if (auth()->check()) {
            auth()->user()->updateNotificationPreference($type, $channel, $enabled);
            
            // Update local state
            $this->preferences[$type]['channels'][$channel]['enabled'] = $enabled;
            
            $this->dispatch('preference-updated', [
                'type' => $type,
                'channel' => $channel,
                'enabled' => $enabled,
            ]);
        }
    }

    public function enableAllForType($type)
    {
        if (auth()->check()) {
            $channels = config('notifyx.channels', []);
            
            foreach ($channels as $channel) {
                auth()->user()->updateNotificationPreference($type, $channel, true);
                $this->preferences[$type]['channels'][$channel]['enabled'] = true;
            }
            
            $this->dispatch('type-preferences-updated', ['type' => $type, 'enabled' => true]);
        }
    }

    public function disableAllForType($type)
    {
        if (auth()->check()) {
            $channels = config('notifyx.channels', []);
            
            foreach ($channels as $channel) {
                auth()->user()->updateNotificationPreference($type, $channel, false);
                $this->preferences[$type]['channels'][$channel]['enabled'] = false;
            }
            
            $this->dispatch('type-preferences-updated', ['type' => $type, 'enabled' => false]);
        }
    }

    public function enableAllForChannel($channel)
    {
        if (auth()->check()) {
            foreach ($this->preferences as $type => $config) {
                auth()->user()->updateNotificationPreference($type, $channel, true);
                $this->preferences[$type]['channels'][$channel]['enabled'] = true;
            }
            
            $this->dispatch('channel-preferences-updated', ['channel' => $channel, 'enabled' => true]);
        }
    }

    public function disableAllForChannel($channel)
    {
        if (auth()->check()) {
            foreach ($this->preferences as $type => $config) {
                auth()->user()->updateNotificationPreference($type, $channel, false);
                $this->preferences[$type]['channels'][$channel]['enabled'] = false;
            }
            
            $this->dispatch('channel-preferences-updated', ['channel' => $channel, 'enabled' => false]);
        }
    }

    public function resetToDefaults()
    {
        if (auth()->check()) {
            // Delete all preferences (will default to enabled)
            auth()->user()->notificationPreferences()->delete();
            $this->loadPreferences();
            
            $this->dispatch('preferences-reset');
        }
    }

    public function getChannelsProperty()
    {
        return config('notifyx.channels', []);
    }

    public function getChannelLabelsProperty()
    {
        return [
            'database' => 'In-App',
            'mail' => 'Email',
            'broadcast' => 'Real-time',
            'sms' => 'SMS',
            'slack' => 'Slack',
        ];
    }

    public function render()
    {
        return view('notifyx::livewire.notification-preferences', [
            'channels' => $this->channels,
            'channelLabels' => $this->channelLabels,
        ]);
    }
}
