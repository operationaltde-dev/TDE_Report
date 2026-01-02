<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ChangePassword extends Component
{
    public $current_password;
    public $password;
    public $password_confirmation;

    protected $rules = [
        'current_password' => 'required',
        'password' => 'required|min:8|confirmed',
    ];

    public function updatePassword()
    {
        $this->validate();

        $username = session('username');

        if (!$username) {
            session()->flash('error', 'Session expired. Please login again.');
            return redirect('/login');
        }

        $user = User::where('username', $username)->first();

        if (!$user || !Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password invalid');
            return;
        }

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset([
            'current_password',
            'password',
            'password_confirmation',
        ]);

        session()->flash('success', 'Password updated');
    }

    public function render()
    {
        return view('livewire.auth.change-password')
            ->layout('components.layouts.dashboard')
            ->title('Change Password');
    }
}
