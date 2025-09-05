<?php

namespace Notifyx\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class NotificationDropdown extends Component
{
    public $notifications = [];
    public bool $isOpen = false;
    public bool $loading = false;

    #[On('dropdown-opened')]
    public function loadNotifications()
    {
        $this->loading = true;
        $this->isOpen = true;
        
        if (auth()->check()) {
            $limit = config('notifyx.ui.max_dropdown_items', 5);
            $this->notifications = auth()->user()
                ->getRecentUnreadNotifications($limit)
                ->map(function ($notification) {
                    $data = $notification->data;
                    $typeConfig = config("notifyx.types.{$notification->type_tag}", []);
                    
                    return [
                        'id' => $notification->id,
                        'title' => $data['title'] ?? null,
                        'message' => $data['message'] ?? '',
                        'type' => $notification->type_tag ?? 'info',
                        'icon' => $typeConfig['icon'] ?? 'heroicon-o-bell',
                        'color' => $typeConfig['color'] ?? 'gray',
                        'action_url' => $data['action_url'] ?? null,
                        'action_text' => $data['action_text'] ?? 'View',
                        'created_at' => $notification->created_at->diffForHumans(),
                        'read_at' => $notification->read_at,
                    ];
                })
                ->toArray();
        }
        
        $this->loading = false;
    }

    #[On('notification-pushed')]
    public function onNotificationPushed($notification)
    {
        if ($this->isOpen) {
            $this->loadNotifications();
        }
    }

    public function markAsRead($notificationId)
    {
        if (auth()->check()) {
            auth()->user()->markNotificationAsRead($notificationId);
            
            // Remove from current list
            $this->notifications = array_filter($this->notifications, function ($notification) use ($notificationId) {
                return $notification['id'] !== $notificationId;
            });

            $this->dispatch('notifications-read');
        }
    }

    public function markAllAsRead()
    {
        if (auth()->check()) {
            auth()->user()->markAllNotificationsAsRead();
            $this->notifications = [];
            $this->dispatch('notifications-read');
        }
    }

    public function viewAll()
    {
        return $this->redirect(route('notifications.index'), navigate: true);
    }

    public function close()
    {
        $this->isOpen = false;
        $this->notifications = [];
    }

    public function visitNotification($notificationId, $url = null)
    {
        // Mark as read when visiting
        if (config('notifyx.ui.auto_mark_read', true)) {
            $this->markAsRead($notificationId);
        }

        if ($url) {
            return $this->redirect($url, navigate: true);
        }
    }

    public function render()
    {
        return view('notifyx::livewire.notification-dropdown');
    }
}
