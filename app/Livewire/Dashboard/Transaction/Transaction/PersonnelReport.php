<?php

namespace App\Livewire\Dashboard\Transaction;

use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PersonnelReport as PersonnelReportExport;
use Carbon\Carbon;

use App\Models\EventLog as EventLogModel;
use App\Models\PartitionMaster;

class PersonnelReport extends Component
{
    use WithPagination;

    public $search = '';
    public $location = '';
    public $startDate;
    public $endDate;
    public int $perPage = 10;

    protected $queryString = [
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
        return EventLogModel::with('credential.owner.group')
            ->where('credentialid', '!=', 0)
            ->when($this->location !== '', function ($query) {
                $query->where('partitionid', $this->location);
            })
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('msgdescription', 'ilike', '%' . $this->search . '%')
                    ->orWhere('hardwaredescription', 'ilike', '%' . $this->search . '%')
                    ->orWhere('ts', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('credential', function ($qg) {
                        $qg->where('cardnumber', 'ilike', '%' . $this->search . '%');
                    })
                    ->orWhereHas('credential.owner', function ($qg) {
                        $qg->where('description', 'ilike', '%' . $this->search . '%');
                    })
                    ->orWhereHas('credential.owner.group', function ($qg) {
                        $qg->where('description', 'ilike', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->startDate && $this->endDate, function ($q) {
                $q->whereBetween('ts', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59'
                ]);
            })
            ->orderBy('ts', 'desc');
    }

    public function exportPdf()
    {
        $reports = $this->baseQuery()->get();

        $pdf = Pdf::loadView('pdf.personnel-report', [
            'reports'   => $reports,
            'startDate' => $this->startDate,
            'endDate'   => $this->endDate,
            'location'  => $this->location,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'personnel-report.pdf'
        );
    }

    public function exportExcel()
    {
        return Excel::download(
            new PersonnelReportExport($this->baseQuery()),
            'personnel-report.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.dashboard.transaction.personnel-report', [
            'personnel_reports' => $this->baseQuery()->paginate($this->perPage),
            'locations' => PartitionMaster::where('isdeleted', false)->get()
        ])
        ->layout('components.layouts.dashboard')
        ->title('Personnel Report');
    }
}
