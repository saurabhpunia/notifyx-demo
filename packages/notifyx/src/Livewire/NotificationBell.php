<?php

namespace Notifyx\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class NotificationBell extends Component{

    public $notification = null;
    public bool $showDropdown = false;

    public function getUnreadCountProperty()
    {
        if (auth()->check()) {
            return auth()->user()->getUnreadCount();
        }
        return 0;
    }

    #[On('notification-pushed')]
    public function onNotificationPushed($notification)
    {
        $this->dispatch('bell-notification-received', $notification);
    }

    #[On('notifications-read')]
    public function onNotificationsRead()
    {
        // The computed property will automatically refresh
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
        
        if ($this->showDropdown) {
            $this->dispatch('dropdown-opened');
        } else {
            $this->dispatch('dropdown-closed');
        }
    }

    public function closeDropdown()
    {
        $this->showDropdown = false;
        $this->dispatch('dropdown-closed');
    }

    public function render()
    {
        return view('notifyx::livewire.notification-bell');
    }
}
