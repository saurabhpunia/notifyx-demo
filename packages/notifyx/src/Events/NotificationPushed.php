<?php

namespace Notifyx\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\SerializesModels;

class NotificationPushed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DatabaseNotification $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(DatabaseNotification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channelName = config('notifyx.broadcasting.channel_name', 'notifications');
        
        if (config('notifyx.multitenant')) {
            $tenantId = $this->notification->tenant_id ?? 'default';
            return [
                new PrivateChannel("{$channelName}.{$this->notification->notifiable_id}.{$tenantId}")
            ];
        }

        return [
            new PrivateChannel("{$channelName}.{$this->notification->notifiable_id}")
        ];
    }

    /**
     * Get the event name for broadcasting.
     */
    public function broadcastAs(): string
    {
        return config('notifyx.broadcasting.event_name', 'NotificationPushed');
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'notification' => [
                'id' => $this->notification->id,
                'type' => $this->notification->type,
                'type_tag' => $this->notification->type_tag,
                'data' => $this->notification->data,
                'created_at' => $this->notification->created_at->toISOString(),
                'read_at' => $this->notification->read_at?->toISOString(),
                'metadata' => $this->notification->metadata ?? [],
            ],
            'unread_count' => $this->notification->notifiable->getUnreadCount(),
        ];
    }
}
