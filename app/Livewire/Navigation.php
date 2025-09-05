<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Navigation extends Component
{
    public $currentRoute;

    protected $listeners = [
        'navigation-updated' => 'refreshCurrentRoute',
    ];

    public function mount()
    {
        $this->currentRoute = request()->route() ? request()->route()->getName() : '';
    }

    public function updatedCurrentRoute()
    {
        $this->currentRoute = request()->route() ? request()->route()->getName() : '';
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        return $this->redirect('/', navigate: true);
    }

    public function goToDashboard()
    {
        $this->currentRoute = 'dashboard';
        $this->dispatch('navigation-updated');
        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function goToNotifications()
    {
        $this->currentRoute = 'notifications.index';
        $this->dispatch('navigation-updated');
        return $this->redirect(route('notifications.index'), navigate: true);
    }

    public function goToPreferences()
    {
        $this->currentRoute = 'notifications.preferences';
        $this->dispatch('navigation-updated');
        return $this->redirect(route('notifications.preferences'), navigate: true);
    }

    public function goToLogin()
    {
        return $this->redirect('/login', navigate: true);
    }

    public function refreshCurrentRoute()
    {
        $this->currentRoute = request()->route() ? request()->route()->getName() : '';
    }

    public function render()
    {
        return view('livewire.navigation');
    }
}
