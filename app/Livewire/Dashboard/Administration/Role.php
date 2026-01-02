<?php

namespace App\Livewire\Dashboard\Administration;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role as RoleModel;

class Role extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

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
        return view('livewire.dashboard.administration.role', [
            'roles' => RoleModel::where('name', 'ilike', '%'.$this->search.'%')
                ->orderBy('name')
                ->paginate($this->perPage)
        ])->layout('components.layouts.dashboard')->title('Roles');
    }
}
