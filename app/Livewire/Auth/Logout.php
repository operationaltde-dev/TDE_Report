<?php

namespace App\Livewire\Auth;

use Livewire\Component;

class Logout extends Component
{
    public function logout()
    {
        session()->forget('logged_in');
        return redirect('/');
    }

    public function render()
    {
        return view('livewire.auth.logout')
            ->layout('components.layouts.auth')
            ->title('Logout');
    }
}
