<?php

namespace Notifyx\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;
use Notifyx\Models\NotificationPreference;

trait HasNotifyx
{
    /**
     * Get all notifications for this user
     */
    public function getNotifications($limit = null, $offset = 0)
    {
        $query = $this->notifications()
            ->when(config('notifyx.multitenant'), function ($query) {
                $tenantId = $this->getCurrentTenantId();
                if ($tenantId) {
                    return $query->where('data->tenant_id', $tenantId);
                } else {
                    return $query->where(function($q) {
                        $q->whereNull('data->tenant_id')
                          ->orWhereJsonMissing('data->tenant_id');
                    });
                }
            })
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit)->offset($offset);
        }

        return $query->get();
    }

    /**
     * Get paginated notifications
     */
    public function getPaginatedNotifications(int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('notifyx.per_page', 15);

        return $this->notifications()
            ->when(config('notifyx.multitenant'), function ($query) {
                $tenantId = $this->getCurrentTenantId();
                if ($tenantId) {
                    return $query->where('data->tenant_id', $tenantId);
                } else {
                    return $query->where(function($q) {
                        $q->whereNull('data->tenant_id')
                          ->orWhereJsonMissing('data->tenant_id');
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(): int
    {
        return $this->unreadNotifications()
            ->when(config('notifyx.multitenant'), function ($query) {
                $tenantId = $this->getCurrentTenantId();
                if ($tenantId) {
                    return $query->where('data->tenant_id', $tenantId);
                } else {
                    return $query->where(function($q) {
                        $q->whereNull('data->tenant_id')
                          ->orWhereJsonMissing('data->tenant_id');
                    });
                }
            })
            ->count();
    }

    /**
     * Get recent unread notifications for dropdown
     */
    public function getRecentUnreadNotifications(int $limit = 5)
    {
        return $this->unreadNotifications()
            ->when(config('notifyx.multitenant'), function ($query) {
                $tenantId = $this->getCurrentTenantId();
                if ($tenantId) {
                    return $query->where('data->tenant_id', $tenantId);
                } else {
                    return $query->where(function($q) {
                        $q->whereNull('data->tenant_id')
                          ->orWhereJsonMissing('data->tenant_id');
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search notifications by query string
     */
    public function searchNotifications(string $query = '', $filters = [], int $limit = null)
    {
        $notifications = $this->notifications()
            ->when(config('notifyx.multitenant'), function ($queryBuilder) {
                $tenantId = $this->getCurrentTenantId();
                if ($tenantId) {
                    return $queryBuilder->where('data->tenant_id', $tenantId);
                } else {
                    return $queryBuilder->where(function($q) {
                        $q->whereNull('data->tenant_id')
                          ->orWhereJsonMissing('data->tenant_id');
                    });
                }
            });

        // Apply search query if provided
        if (!empty($query)) {
            $notifications->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('data->title', 'like', "%{$query}%")
                    ->orWhere('data->message', 'like', "%{$query}%")
                    ->orWhere('data->type', 'like', "%{$query}%");
            });
        }

        // Handle filters parameter (could be array or integer for backward compatibility)
        if (is_array($filters)) {
            // Apply type filter
            if (!empty($filters['type'])) {
                $notifications->where('data->type', $filters['type']);
            }

            // Apply read status filter
            if (isset($filters['read_status'])) {
                if ($filters['read_status'] === 'read') {
                    $notifications->whereNotNull('read_at');
                } elseif ($filters['read_status'] === 'unread') {
                    $notifications->whereNull('read_at');
                }
            }

            // Apply date filters
            if (!empty($filters['date_from'])) {
                $notifications->where('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $notifications->where('created_at', '<=', $filters['date_to']);
            }
        } elseif (is_int($filters)) {
            // Backward compatibility: treat second parameter as limit
            $limit = $filters;
        }

        $notifications->orderBy('created_at', 'desc');

        if ($limit) {
            $notifications->limit($limit);
        }

        return $notifications->get();
    }

    /**
     * Search notifications with pagination
     */
    public function searchNotificationsPaginated(string $query = '', $filters = [], int $perPage = null)
    {
        $perPage = $perPage ?? config('notifyx.per_page', 15);
        
        $notifications = $this->notifications()
            ->when(config('notifyx.multitenant'), function ($queryBuilder) {
                $tenantId = $this->getCurrentTenantId();
                if ($tenantId) {
                    return $queryBuilder->where('data->tenant_id', $tenantId);
                } else {
                    return $queryBuilder->where(function($q) {
                        $q->whereNull('data->tenant_id')
                          ->orWhereJsonMissing('data->tenant_id');
                    });
                }
            });

        // Apply search query if provided
        if (!empty($query)) {
            $notifications->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('data->title', 'like', "%{$query}%")
                    ->orWhere('data->message', 'like', "%{$query}%")
                    ->orWhere('data->type', 'like', "%{$query}%");
            });
        }

        // Handle filters parameter
        if (is_array($filters)) {
            // Apply type filter
            if (!empty($filters['type'])) {
                $notifications->where('data->type', $filters['type']);
            }

            // Apply read status filter
            if (isset($filters['read_status'])) {
                if ($filters['read_status'] === 'read') {
                    $notifications->whereNotNull('read_at');
                } elseif ($filters['read_status'] === 'unread') {
                    $notifications->whereNull('read_at');
                }
            }

            // Apply date filters
            if (!empty($filters['date_from'])) {
                $notifications->where('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $notifications->where('created_at', '<=', $filters['date_to']);
            }
        }

        return $notifications->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Mark a specific notification as read
     */
    public function markNotificationAsRead(string $notificationId): bool
    {
        $notification = $this->notifications()
            ->where('id', $notificationId)
            ->when(config('notifyx.multitenant'), function ($query) {
                $tenantId = $this->getCurrentTenantId();
                if ($tenantId) {
                    return $query->where('data->tenant_id', $tenantId);
                } else {
                    return $query->where(function($q) {
                        $q->whereNull('data->tenant_id')
                          ->orWhereJsonMissing('data->tenant_id');
                    });
                }
            })
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead(): int
    {
        $notifications = $this->unreadNotifications()
            ->when(config('notifyx.multitenant'), function ($query) {
                $tenantId = $this->getCurrentTenantId();
                if ($tenantId) {
                    return $query->where('data->tenant_id', $tenantId);
                } else {
                    return $query->where(function($q) {
                        $q->whereNull('data->tenant_id')
                          ->orWhereJsonMissing('data->tenant_id');
                    });
                }
            })
            ->get();

        $count = $notifications->count();

        foreach ($notifications as $notification) {
            if (method_exists($notification, 'markAsRead')) {
                $notification->markAsRead();
            }
        }

        return $count;
    }

    /**
     * Delete a specific notification
     */
    public function deleteNotification(string $notificationId): bool
    {
        $notification = $this->notifications()
            ->where('id', $notificationId)
            ->when(config('notifyx.multitenant'), function ($query) {
                $tenantId = $this->getCurrentTenantId();
                if ($tenantId) {
                    return $query->where('data->tenant_id', $tenantId);
                } else {
                    return $query->where(function($q) {
                        $q->whereNull('data->tenant_id')
                          ->orWhereJsonMissing('data->tenant_id');
                    });
                }
            })
            ->first();

        if ($notification) {
            $notification->delete();
            return true;
        }

        return false;
    }

    /**
     * Delete all notifications
     */
    public function deleteAllNotifications(): int
    {
        $notifications = $this->notifications()
            ->when(config('notifyx.multitenant'), function ($query) {
                $tenantId = $this->getCurrentTenantId();
                if ($tenantId) {
                    return $query->where('data->tenant_id', $tenantId);
                } else {
                    return $query->where(function($q) {
                        $q->whereNull('data->tenant_id')
                          ->orWhereJsonMissing('data->tenant_id');
                    });
                }
            })
            ->get();

        $count = $notifications->count();
        
        $notifications->each(function ($notification) {
            $notification->delete();
        });

        return $count;
    }

    /**
     * Get notifications by type
     */
    public function getNotificationsByType(string $type, int $limit = null)
    {
        $query = $this->notifications()
            ->when(config('notifyx.multitenant'), function ($query) {
                $tenantId = $this->getCurrentTenantId();
                if ($tenantId) {
                    return $query->where('data->tenant_id', $tenantId);
                } else {
                    return $query->where(function($q) {
                        $q->whereNull('data->tenant_id')
                          ->orWhereJsonMissing('data->tenant_id');
                    });
                }
            })
            ->where('data->type', $type)
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Check if user has enabled a specific notification type and channel
     */
    public function hasEnabledNotification(string $type, string $channel): bool
    {
        return NotificationPreference::isEnabled($this->id, $type, $channel);
    }

    /**
     * Get notification preferences relation
     */
    public function notificationPreferences()
    {
        return $this->hasMany(NotificationPreference::class);
    }

    /**
     * Get current tenant ID
     */
    protected function getCurrentTenantId()
    {
        if (!config('notifyx.multitenant')) {
            return null;
        }

        $resolver = config('notifyx.tenant_resolver');
        
        if ($resolver && is_callable($resolver)) {
            return $resolver();
        }

        return null;
    }

    /**
     * Get notification statistics for the user
     */
    public function getNotificationStats(): array
    {
        $baseQuery = $this->notifications()
            ->when(config('notifyx.multitenant'), function ($query) {
                $tenantId = $this->getCurrentTenantId();
                if ($tenantId) {
                    return $query->where('data->tenant_id', $tenantId);
                } else {
                    return $query->where(function($q) {
                        $q->whereNull('data->tenant_id')
                          ->orWhereJsonMissing('data->tenant_id');
                    });
                }
            });

        $total = $baseQuery->count();
        $unread = $baseQuery->whereNull('read_at')->count();
        
        return [
            'total' => $total,
            'unread' => $unread,
            'read' => $total - $unread,
        ];
    }

    /**
     * Get notification preferences for the user
     */
    public function getNotificationPreferences()
    {
        $preferences = NotificationPreference::where('user_id', $this->id)
            ->when(config('notifyx.multitenant'), function ($query) {
                $tenantId = $this->getCurrentTenantId();
                if ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                } else {
                    return $query->where(function($q) {
                        $q->whereNull('tenant_id')
                          ->orWhere('tenant_id', '');
                    });
                }
            })
            ->get()
            ->groupBy('type');

        $types = config('notifyx.types', []);
        $channels = config('notifyx.channels', ['database', 'mail', 'broadcast']);
        $result = [];

        foreach ($types as $type => $config) {
            $typePreferences = $preferences->get($type, collect());
            
            $result[$type] = [
                'label' => $config['label'] ?? ucfirst($type),
                'description' => $config['description'] ?? '',
                'icon' => $config['icon'] ?? 'heroicon-o-bell',
                'color' => $config['color'] ?? 'gray',
                'channels' => []
            ];

            foreach ($channels as $channel) {
                $channelPreference = $typePreferences->where('channel', $channel)->first();
                $result[$type]['channels'][$channel] = [
                    'enabled' => $channelPreference ? $channelPreference->is_enabled : ($config['default_enabled'] ?? true),
                    'label' => ucfirst($channel),
                ];
            }
        }

        return $result;
    }

    /**
     * Update notification preference for a specific type and channel
     */
    public function updateNotificationPreference(string $type, string $channel, bool $enabled)
    {
        $tenantId = $this->getCurrentTenantId();
        
        NotificationPreference::updateOrCreate(
            [
                'user_id' => $this->id,
                'type' => $type,
                'channel' => $channel,
                'tenant_id' => $tenantId,
            ],
            [
                'is_enabled' => $enabled,
            ]
        );
    }

    /**
     * Update multiple notification preferences at once
     */
    public function updateNotificationPreferences(array $preferences): bool
    {
        foreach ($preferences as $type => $channels) {
            foreach ($channels as $channel => $enabled) {
                $this->updateNotificationPreference($type, $channel, $enabled);
            }
        }
        return true;
    }
}
