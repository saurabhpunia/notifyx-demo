<?php

namespace Notifyx\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Notifyx\Services\Notifyx to(\Illuminate\Database\Eloquent\Model $user)
 * @method static \Notifyx\Services\Notifyx with(string $message)
 * @method static \Notifyx\Services\Notifyx title(string $title)
 * @method static \Notifyx\Services\Notifyx type(string $type)
 * @method static \Notifyx\Services\Notifyx via(array $channels)
 * @method static \Notifyx\Services\Notifyx data(array $data)
 * @method static \Notifyx\Services\Notifyx metadata(array $metadata)
 * @method static \Notifyx\Services\Notifyx action(string $url, string $text = 'View')
 * @method static void send()
 * @method static void sendForced()
 */
class Notifyx extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'notifyx';
    }
}
