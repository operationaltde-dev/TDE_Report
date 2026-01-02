<?php

namespace App\Livewire\Dashboard\Administration;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User as UserModel;

class User extends Component
{
    use WithPagination;

    public $search = '';
    public int $perPage = 10;
    
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.dashboard.administration.user', [
            'users' => UserModel::where('username', 'ilike', '%'.$this->search.'%')
                ->orderBy('username')
                ->paginate($this->perPage)
        ])->layout('components.layouts.dashboard')->title('Users');
    }
}
