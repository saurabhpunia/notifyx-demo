<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

Route::get('/', function (): RedirectResponse {
    return redirect('login');
});

// Simple auth routes for demo
Route::get('/login', \App\Livewire\Login::class)->name('login');

Route::post('/login', function (): RedirectResponse {
    // Create or get first user for demo
    $user = User::firstOrCreate(
        ['email' => 'demo@example.com'],
        [
            'name' => 'Demo User',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]
    );
    
    Auth::login($user);
    
    return redirect('/dashboard');
});

Route::post('/logout', function (): RedirectResponse {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Demo routes for testing notifications
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    Route::post('/send-test-notification', function () {
        $user = auth()->user();
        
        notify($user)
            ->title('Test Notification')
            ->with('This is a test notification from Notifyx!')
            ->type('message')
            ->via(['database', 'broadcast'])
            ->action('/dashboard', 'View Dashboard')
            ->send();
            
        return back()->with('success', 'Test notification sent!');
    })->name('send-test-notification');

    Route::post('/send-billing-notification', function () {
        $user = auth()->user();
        
        notify($user)
            ->title('Payment Received')
            ->with('Your payment of $29.99 has been processed successfully.')
            ->type('billing')
            ->via(['database', 'broadcast', 'mail'])
            ->action('/dashboard', 'View Invoice')
            ->data(['amount' => 29.99, 'invoice_id' => 'INV-001'])
            ->send();
            
        return back()->with('success', 'Billing notification sent!');
    })->name('send-billing-notification');

    Route::post('/send-system-notification', function () {
        $user = auth()->user();
        
        notify($user)
            ->title('System Update')
            ->with('System maintenance is scheduled for tonight at 2 AM EST.')
            ->type('system')
            ->via(['database', 'broadcast'])
            ->send();
            
        return back()->with('success', 'System notification sent!');
    })->name('send-system-notification');

    // Test route for pagination (auto login)
    Route::get('/test-notifications', function () {
        // Auto-login demo user
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        
        Auth::login($user);
        
        return redirect('/notifications');
    });

    // Test route to debug notification bell
    Route::get('/test-bell', function () {
        $user = App\Models\User::where('email', 'demo@example.com')->first();
        if ($user) {
            Auth::login($user);
        }
        return view('test-bell');
    });
});

// Test route (unprotected for debugging)
Route::get('/test-bell-debug', function () {
    // Create or get first user for demo
    $user = User::firstOrCreate(
        ['email' => 'demo@example.com'],
        [
            'name' => 'Demo User',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]
    );
    
    Auth::login($user);
    
    return view('test-bell');
});

// Login route
