<?php

if (! function_exists('notify')) {
    /**
     * Create a new Notifyx instance
     *
     * @param \Illuminate\Database\Eloquent\Model|null $user
     * @return \Notifyx\Services\Notifyx
     */
    function notify($user = null) {
        $notifier = app('notifyx');
        
        if ($user) {
            $notifier->to($user);
        }
        
        return $notifier;
    }
}
