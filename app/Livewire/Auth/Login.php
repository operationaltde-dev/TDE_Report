<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class Login extends Component
{
    public $username;
    public $password;

    public function login()
    {
        $this->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $this->username)->first();

        if (!$user || $this->password!="password") {
            return $this->addError('alert', 'Username or password invalid');
        }

        session([
            'logged_in' => true,
            'username' => $user->username,
        ]);

       return redirect('/home');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.auth')
            ->title('Login');
    }
}
