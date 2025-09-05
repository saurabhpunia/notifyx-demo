<?php

namespace Notifyx\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class NotificationPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $typeFilter = '';
    public string $readStatusFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'readStatusFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingReadStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->readStatusFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function markAsRead($notificationId)
    {
        if (auth()->check()) {
            auth()->user()->markNotificationAsRead($notificationId);
            $this->dispatch('notification-marked-read');
        }
    }

    public function markAllAsRead()
    {
        if (auth()->check()) {
            auth()->user()->markAllNotificationsAsRead();
            $this->dispatch('all-notifications-marked-read');
        }
    }

    public function deleteNotification($notificationId)
    {
        if (auth()->check()) {
            auth()->user()->notifications()->where('id', $notificationId)->delete();
            $this->dispatch('notification-deleted');
        }
    }

    public function getNotificationTypesProperty()
    {
        return config('notifyx.types', []);
    }

    public function render()
    {
        $notifications = collect();
        $unreadCount = 0;
        
        if (auth()->check()) {
            $filters = array_filter([
                'type' => $this->typeFilter,
                'read_status' => $this->readStatusFilter,
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo,
            ]);

            // Get all notifications first
            $allNotifications = auth()->user()->searchNotifications($this->search, $filters);
            
            // Create manual pagination
            $perPage = config('notifyx.per_page', 15);
            $currentPage = $this->getPage();
            $total = $allNotifications->count();
            
            // Get the subset for current page
            $currentItems = $allNotifications->forPage($currentPage, $perPage);
            
            // Create paginator
            $notifications = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $total,
                $perPage,
                $currentPage,
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
            
            $unreadCount = auth()->user()->getUnreadCount();
        }

        return view('notifyx::livewire.notification-page', [
            'notifications' => $notifications,
            'notificationTypes' => $this->notificationTypes,
            'unreadCount' => $unreadCount,
        ]);
    }
}
