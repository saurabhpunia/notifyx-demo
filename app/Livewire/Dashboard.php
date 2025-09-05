<?php

namespace App\Livewire;

use Livewire\Component;
use Universal\NotificationCenter\Facades\UniversalNotifier;

class Dashboard extends Component
{
    public $showSuccessMessage = false;
    public $successMessage = '';

    protected $listeners = [
        'notification-sent' => 'onNotificationSent',
        'hide-message' => 'hideMessage',
    ];

    public function sendTestNotification()
    {
        if (!auth()->check()) {
            return;
        }

        try {
            notify(auth()->user())
                ->title('Test Notification')
                ->with('This is a test message notification from the Universal Notification Center!')
                ->type('message')
                ->via(['database', 'broadcast'])
                ->send();

            // Get the latest notification for the user
            $notification = auth()->user()->notifications()->latest()->first();

            $this->showSuccessMessage('Test notification sent successfully!');
            $this->dispatch('notification-sent', notification: $notification);
        } catch (\Exception $e) {
            $this->showSuccessMessage('Failed to send notification: ' . $e->getMessage());
        }
    }

    public function sendBillingNotification()
    {
        if (!auth()->check()) {
            return;
        }

        try {
            notify(auth()->user())
                ->title('Payment Processed')
                ->with('Your monthly subscription payment of $29.99 has been successfully processed.')
                ->type('billing')
                ->via(['database', 'mail', 'broadcast'])
                ->action('View Invoice', route('dashboard'))
                ->data([
                    'amount' => '$29.99',
                    'invoice_id' => 'INV-' . rand(1000, 9999),
                ])
                ->send();

            $notification = auth()->user()->notifications()->latest()->first();

            $this->showSuccessMessage('Billing notification sent successfully!');
            $this->dispatch('notification-sent', notification: $notification);
        } catch (\Exception $e) {
            $this->showSuccessMessage('Failed to send billing notification: ' . $e->getMessage());
        }
    }

    public function sendSystemNotification()
    {
        if (!auth()->check()) {
            return;
        }

        try {
            notify(auth()->user())
                ->title('System Maintenance')
                ->with('Scheduled maintenance will occur tonight from 2:00 AM to 4:00 AM EST. Some features may be temporarily unavailable.')
                ->type('system')
                ->via(['database', 'broadcast'])
                ->send();

            $notification = auth()->user()->notifications()->latest()->first();

            $this->showSuccessMessage('System notification sent successfully!');
            $this->dispatch('notification-sent', notification: $notification);
        } catch (\Exception $e) {
            $this->showSuccessMessage('Failed to send system notification: ' . $e->getMessage());
        }
    }

    public function onNotificationSent()
    {
        // This method can be used to refresh any data when notifications are sent
    }

    private function showSuccessMessage($message)
    {
        $this->successMessage = $message;
        $this->showSuccessMessage = true;
        
        // Auto-hide using JavaScript timer instead
        $this->dispatch('show-success-message');
    }

    public function hideMessage()
    {
        $this->showSuccessMessage = false;
        $this->successMessage = '';
    }

    public function getUnreadCountProperty()
    {
        return auth()->check() ? auth()->user()->getUnreadCount() : 0;
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'unreadCount' => $this->unreadCount,
        ]);
    }
}
