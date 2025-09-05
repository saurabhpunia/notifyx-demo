<?php

namespace App\Livewire;

use Livewire\Component;

class AppShell extends Component
{
    public $currentPage = 'dashboard';
    
    protected $listeners = [
        'navigate-to' => 'navigateTo',
        'notification-sent' => 'refreshNavigation',
        'notifications-read' => 'refreshNavigation',
    ];

    public function mount()
    {
        $this->currentPage = request()->route()->getName() ?? 'dashboard';
    }

    public function navigateTo($page)
    {
        $this->currentPage = $page;
    }

    public function refreshNavigation()
    {
        dd('Navigation refreshed');
        // Refresh the entire component to update unread counts, etc.
        $this->skipRender();
    }

    public function render()
    {
        return view('livewire.app-shell')->layout('components.layouts.minimal');
    }
}
