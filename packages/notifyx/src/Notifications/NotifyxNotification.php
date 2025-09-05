<?php

namespace Notifyx\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Notifyx\Events\NotificationPushed;

class NotifyxNotification extends Notification
{
    use Queueable;

    public string $message;
    public string $type;
    public array $channels;
    public array $data;
    public array $metadata;
    public ?string $title;
    public ?string $actionUrl;
    public ?string $actionText;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        string $message,
        string $type = 'info',
        array $channels = ['database'],
        array $data = [],
        array $metadata = [],
        ?string $title = null,
        ?string $actionUrl = null,
        ?string $actionText = null
    ) {
        $this->message = $message;
        $this->type = $type;
        $this->channels = $channels;
        $this->data = $data;
        $this->metadata = $metadata;
        $this->title = $title;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
            'data' => $this->data,
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $data = $this->toArray($notifiable);
        
        // Add type_tag for easier filtering
        $data['type_tag'] = $this->type;
        
        // Add tenant ID if multitenancy is enabled
        if (config('notifyx.multitenant')) {
            $tenantResolver = config('notifyx.tenant_resolver');
            if ($tenantResolver && is_callable($tenantResolver)) {
                $data['tenant_id'] = $tenantResolver();
            }
        }

        // Add metadata with type configuration
        $typeConfig = config("notifyx.types.{$this->type}", []);
        $data['metadata'] = array_merge($this->metadata, [
            'icon' => $typeConfig['icon'] ?? null,
            'color' => $typeConfig['color'] ?? 'gray',
            'label' => $typeConfig['label'] ?? ucfirst($this->type),
        ]);

        return $data;
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
            'data' => $this->data,
            'created_at' => now()->toISOString(),
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title ?? 'New Notification')
            ->line($this->message);

        if ($this->actionUrl && $this->actionText) {
            $mail->action($this->actionText, $this->actionUrl);
        }

        return $mail;
    }

    /**
     * Handle the notification after it has been stored.
     */
    public function afterCommit(): void
    {
        // Dispatch broadcasting event for real-time updates
        if (in_array('broadcast', $this->channels) && 
            config('notifyx.broadcasting.enabled', true)) {
            
            // Find the database notification that was just created
            $databaseNotification = $this->getStoredNotification();
            
            if ($databaseNotification) {
                broadcast(new NotificationPushed($databaseNotification))->toOthers();
            }
        }
    }

    /**
     * Get additional data to store with the notification.
     */
    public function databaseType(object $notifiable): string
    {
        return static::class;
    }

    /**
     * Get the stored database notification.
     */
    protected function getStoredNotification()
    {
        // This is a simplified approach - in a real implementation,
        // you might want to use a more reliable method to get the notification
        return null;
    }

    /**
     * Customize the notification data before storing.
     */
    protected function getData(): array
    {
        $typeConfig = config("notifyx.types.{$this->type}", []);
        
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
            'data' => $this->data,
            'type_tag' => $this->type,
            'metadata' => array_merge($this->metadata, [
                'icon' => $typeConfig['icon'] ?? null,
                'color' => $typeConfig['color'] ?? 'gray',
                'label' => $typeConfig['label'] ?? ucfirst($this->type),
            ]),
        ];
    }
}
