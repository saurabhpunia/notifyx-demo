<?php

namespace Notifyx\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Notifyx\Notifications\NotifyxNotification;

class Notifyx
{
    protected $user;
    protected $message;
    protected $type = 'info';
    protected $channels = ['database'];
    protected $data = [];
    protected $metadata = [];
    protected $title;
    protected $actionUrl;
    protected $actionText;

    /**
     * Set the user to notify
     */
    public function to(Model $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Set the notification message
     */
    public function with(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set the notification title
     */
    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set the notification type
     */
    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set the delivery channels
     */
    public function via(array $channels): self
    {
        $this->channels = $channels;
        return $this;
    }

    /**
     * Set additional data
     */
    public function data(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set metadata
     */
    public function metadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Set action URL and text
     */
    public function action(string $url, string $text = 'View'): self
    {
        $this->actionUrl = $url;
        $this->actionText = $text;
        return $this;
    }

    /**
     * Send the notification
     */
    public function send(): void
    {
        if (!$this->user || !$this->message) {
            throw new \InvalidArgumentException('User and message are required');
        }

        // Check user preferences for each channel
        $enabledChannels = [];
        foreach ($this->channels as $channel) {
            if ($this->user->hasEnabledNotification($this->type, $channel)) {
                $enabledChannels[] = $channel;
            }
        }

        if (empty($enabledChannels)) {
            return; // User has disabled all channels for this type
        }

        $notification = new NotifyxNotification(
            $this->message,
            $this->type,
            $enabledChannels,
            $this->data,
            $this->metadata,
            $this->title,
            $this->actionUrl,
            $this->actionText
        );

        Notification::send($this->user, $notification);

        // Reset for next use
        $this->reset();
    }

    /**
     * Send notification without checking preferences (for system notifications)
     */
    public function sendForced(): void
    {
        if (!$this->user || !$this->message) {
            throw new \InvalidArgumentException('User and message are required');
        }

        $notification = new NotifyxNotification(
            $this->message,
            $this->type,
            $this->channels,
            $this->data,
            $this->metadata,
            $this->title,
            $this->actionUrl,
            $this->actionText
        );

        Notification::send($this->user, $notification);

        // Reset for next use
        $this->reset();
    }

    /**
     * Reset the notifier state
     */
    protected function reset(): void
    {
        $this->user = null;
        $this->message = null;
        $this->type = 'info';
        $this->channels = ['database'];
        $this->data = [];
        $this->metadata = [];
        $this->title = null;
        $this->actionUrl = null;
        $this->actionText = null;
    }
}

/**
 * Helper function to start notification building
 */
function notify(Model $user = null): Notifyx
{
    $notifier = app(Notifyx::class);
    
    if ($user) {
        $notifier->to($user);
    }
    
    return $notifier;
}
