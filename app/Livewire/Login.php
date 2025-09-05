<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;
    public $errorMessage = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            request()->session()->regenerate();
            return $this->redirect(route('dashboard'), navigate: true);
        }

        $this->errorMessage = 'The provided credentials do not match our records.';
        $this->password = '';
    }

    public function goToDashboard()
    {
        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function mount()
    {
        if (Auth::check()) {
            return $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.login')->layout('components.layouts.app');
    }
}
