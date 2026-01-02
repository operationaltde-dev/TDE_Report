<?php

namespace App\Livewire\Dashboard\Transaction;

use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ListAuthorize as ListAuthorizeExport;
use Carbon\Carbon;

use App\Models\EventLog as EventLogModel;
use App\Models\Credential;
use App\Models\PartitionMaster;

class ListAuthorize extends Component
{
    use WithPagination;

    public $search = '';
    public $location = '';
    public $startDate;
    public $endDate;
    public int $perPage = 10;

    protected $queryString = [
        'search'  => ['except' => ''],
        'location'  => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate'   => ['except' => ''],
    ];

    // public function mount()
    // {
    //     if ($this->startDate && $this->endDate) {
    //         return;
    //     }
        
    //     $today = Carbon::today()->format('Y-m-d');

    //     $this->startDate = $today;
    //     $this->endDate   = $today;
    // }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedLocation()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    private function baseQuery()
    {
        return Credential::with('owner')
            ->where('isdeleted', 0)
            ->when($this->location !== '', function ($query) {
                $query->where('partitionid', $this->location);
            })
            ->when($this->search !== '', function ($query) {
                $query->whereHas('owner', function ($qg) {
                    $qg->where('description', 'ilike', '%' . $this->search . '%');
                })
                ->orWhereHas('owner.group', function ($qg) {
                    $qg->where('description', 'ilike', '%' . $this->search . '%');
                });
            });
    }

    public function exportPdf()
    {
        $reports = $this->baseQuery()->get();

        $pdf = Pdf::loadView('pdf.list-authorize', [
            'reports'   => $reports,
            'startDate' => $this->startDate,
            'endDate'   => $this->endDate,
            'location'  => $this->location,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'list-authorize.pdf'
        );
    }

    public function exportExcel()
    {
        return Excel::download(
            new ListAuthorizeExport($this->baseQuery()),
            'list-authorize.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.dashboard.transaction.list-authorize', [
            'list_authorize' => $this->baseQuery()->paginate($this->perPage),
            'locations' => PartitionMaster::where('isdeleted', false)->get()
        ])
        ->layout('components.layouts.dashboard')
        ->title('List Authorize');
    }
}