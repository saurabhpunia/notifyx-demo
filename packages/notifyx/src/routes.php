<?php

use Illuminate\Support\Facades\Route;
use Notifyx\Livewire\NotificationPage;
use Notifyx\Livewire\NotificationPreferences;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/notifications', NotificationPage::class)->name('notifications.index');
    Route::get('/notifications/preferences', NotificationPreferences::class)->name('notifications.preferences');
});
