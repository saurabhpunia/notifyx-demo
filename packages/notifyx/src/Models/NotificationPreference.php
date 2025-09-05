<?php

namespace Notifyx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'channel',
        'is_enabled',
        'tenant_id',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /**
     * Scope to filter by tenant if multitenancy is enabled
     */
    public function scopeForTenant($query, $tenantId = null)
    {
        if (config('notifyx.multitenant')) {
            $tenantId = $tenantId ?? $this->getCurrentTenantId();
            return $query->where('tenant_id', $tenantId);
        }

        return $query;
    }

    /**
     * Get the current tenant ID
     */
    protected function getCurrentTenantId()
    {
        $resolver = config('notifyx.tenant_resolver');
        
        if ($resolver && is_callable($resolver)) {
            return $resolver();
        }

        return null;
    }

    /**
     * Check if a user has enabled a specific notification type and channel
     */
    public static function isEnabled(int $userId, string $type, string $channel): bool
    {
        $tenantId = config('notifyx.multitenant') ? 
            static::resolveCurrentTenantId() : null;

        $preference = static::where('user_id', $userId)
            ->where('type', $type)
            ->where('channel', $channel)
            ->when($tenantId, function ($query) use ($tenantId) {
                return $query->where('tenant_id', $tenantId);
            }, function ($query) {
                return $query->whereNull('tenant_id');
            })
            ->first();

        // If no preference exists, default to enabled
        return $preference ? $preference->is_enabled : true;
    }

    /**
     * Set user preference for a notification type and channel
     */
    public static function setPreference(int $userId, string $type, string $channel, bool $isEnabled): self
    {
        $tenantId = config('notifyx.multitenant') ? 
            static::resolveCurrentTenantId() : null;

        // Handle uniqueness at application level for better database compatibility
        $existing = static::where('user_id', $userId)
            ->where('type', $type)
            ->where('channel', $channel)
            ->when($tenantId, function ($query) use ($tenantId) {
                return $query->where('tenant_id', $tenantId);
            }, function ($query) {
                return $query->whereNull('tenant_id');
            })
            ->first();

        if ($existing) {
            $existing->update(['is_enabled' => $isEnabled]);
            return $existing;
        }

        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'channel' => $channel,
            'is_enabled' => $isEnabled,
            'tenant_id' => $tenantId,
        ]);
    }

    /**
     * Get all preferences for a user
     */
    public static function getForUser(int $userId): array
    {
        $tenantId = config('notifyx.multitenant') ? 
            static::resolveCurrentTenantId() : null;

        $preferences = static::where('user_id', $userId)
            ->when($tenantId, function ($query) use ($tenantId) {
                return $query->where('tenant_id', $tenantId);
            }, function ($query) {
                return $query->whereNull('tenant_id');
            })
            ->get()
            ->keyBy(function ($preference) {
                return "{$preference->type}.{$preference->channel}";
            });

        $types = config('notifyx.types', []);
        $channels = config('notifyx.channels', []);
        $result = [];

        foreach ($types as $type => $config) {
            $result[$type] = [
                'label' => $config['label'] ?? ucfirst($type),
                'icon' => $config['icon'] ?? null,
                'color' => $config['color'] ?? 'gray',
                'channels' => []
            ];

            foreach ($channels as $channel) {
                $key = "{$type}.{$channel}";
                $preference = $preferences->get($key);
                
                $result[$type]['channels'][$channel] = [
                    'enabled' => $preference ? $preference->is_enabled : true,
                    'preference_id' => $preference ? $preference->id : null,
                ];
            }
        }

        return $result;
    }

    /**
     * Resolve current tenant ID
     */
    protected static function resolveCurrentTenantId()
    {
        $resolver = config('notifyx.tenant_resolver');
        
        if ($resolver && is_callable($resolver)) {
            return $resolver();
        }

        return null;
    }
}
