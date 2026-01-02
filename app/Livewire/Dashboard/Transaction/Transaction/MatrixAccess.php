<?php

namespace App\Livewire\Dashboard\Transaction;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MatrixAccess as MatrixAccessExport;
use Carbon\Carbon;

use App\Models\EventLog as EventLogModel;
use App\Models\ReaderGroup;
use App\Models\PartitionMaster;

class MatrixAccess extends Component
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

    public function mount()
    {
        if ($this->startDate && $this->endDate) {
            return;
        }
        
        $today = Carbon::today()->format('Y-m-d');

        $this->startDate = $today;
        $this->endDate   = $today;
    }
    
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
        return DB::table('ReaderGroup as rg')
        ->select([
            'c.cardnumber',
            'co.description as full_name',
            'cog.description as group_name',
            'h.description as door_name',
            'pm.description as location',
        ])
        ->join('ReaderGroupMaster as rgm', function ($join) {
            $join->on('rg.readergroupmasterid', '=', 'rgm.id')
                 ->where('rgm.isdeleted', false);
        })
        ->join('Hardware as h', function ($join) {
            $join->on('rg.deviceid', '=', 'h.id')
                 ->where('h.isdeleted', false);
        })
        ->join('CredentialAccess as ca', 'rg.readergroupmasterid', '=', 'ca.accessgroupmasterid')
        ->join('Credential as c', function ($join) {
            $join->on('ca.credentialid', '=', 'c.id')
                 ->where('c.isdeleted', false);
        })
        ->join('CredentialOwner as co', function ($join) {
            $join->on('c.ownerid', '=', 'co.id')
                 ->where('co.isdeleted', false);
        })
        ->join('CredentialOwnerGroup as cog', function ($join) {
            $join->on('co.credentialownergroupid', '=', 'cog.id')
                 ->where('cog.isdeleted', false);
        })
        ->join('PartitionMaster as pm', function ($join) {
            $join->on('c.partitionid', '=', 'pm.id')
                 ->where('pm.isdeleted', false);
        })
        // ->where('rgm.id', 5)
        ->when($this->location !== '', function ($q) {
            $q->where('c.partitionid', $this->location);
        })
        ->when($this->search !== '', function ($q) {
            $q->where('co.description', 'ilike', '%' . $this->search . '%');
            $q->orWhere('cog.description', 'ilike', '%' . $this->search . '%');
        })
        ->orderBy('co.description', 'ASC');
    }

    public function exportPdf()
    {
        $reports = $this->baseQuery()->get();

        $pdf = Pdf::loadView('pdf.matrix-access', [
            'reports'   => $reports,
            'startDate' => $this->startDate,
            'endDate'   => $this->endDate,
            'location'  => $this->location,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'matrix-access.pdf'
        );
    }

    public function exportExcel()
    {
        return Excel::download(
            new MatrixAccessExport($this->baseQuery()),
            'matrix-access.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.dashboard.transaction.matrix-access', [
            'matrix_access' => $this->baseQuery()->paginate($this->perPage),
            'locations' => PartitionMaster::where('isdeleted', false)->get()
        ])
        ->layout('components.layouts.dashboard')
        ->title('Matrix Access');
    }
}
