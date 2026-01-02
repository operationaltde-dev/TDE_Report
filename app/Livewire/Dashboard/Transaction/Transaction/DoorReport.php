<?php

namespace App\Livewire\Dashboard\Transaction;

use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Browsershot\Browsershot;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DoorReport as DoorReportExport;
use Carbon\Carbon;

use App\Models\EventLog as EventLogModel;
use App\Models\PartitionMaster;

class DoorReport extends Component
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
        return EventLogModel::with('credential.owner.group')
            ->where('credentialid', '!=', 0)
            ->when($this->location !== '', function ($query) {
                $query->where('partitionid', $this->location);
            })
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('msgdescription', 'ilike', '%' . $this->search . '%')
                    ->orWhere('ts', 'ilike', '%' . $this->search . '%')
                    ->orWhere('hardwaredescription', 'ilike', '%' . $this->search . '%')
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
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $logoPath = public_path('img/logo.png');
        $src = imagecreatefrompng($logoPath);
        $w = imagesx($src);
        $h = imagesy($src);

        $newH = 80;
        $newW = intval(($w / $h) * $newH);

        $dst = imagecreatetruecolor($newW, $newH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

        ob_start();
        imagepng($dst, null, 1);
        $logoBase64 = base64_encode(ob_get_clean());

        imagedestroy($src);
        imagedestroy($dst);

        $reports = $this->baseQuery()->cursor();
        $perPage   = 20;
        $totalPage = (int) ceil($reports->count() / $perPage);

        $html = view('pdf.door-report', [
            'reports'    => $reports,
            'startDate'  => $this->startDate,
            'endDate'    => $this->endDate,
            'location'   => $this->location,
            'logoBase64' => $logoBase64,
            'totalRows'  => $reports->count(),
        ])->render();

        $path = storage_path('app/door-report.pdf');

        Browsershot::html($html)
	    ->setNodeBinary('C:/Program Files/nodejs/node.exe')
            ->setNpmBinary('C:/Program Files/nodejs/npm.cmd')
	    ->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->noSandbox() 
            ->printBackground(true)
            ->format('A4')
            ->landscape()
            ->margins(10, 10, 10, 10)
            ->save($path);

        return response()->download($path)->deleteFileAfterSend();
    }

    public function exportExcel()
    {
        return Excel::download(
            new DoorReportExport($this->baseQuery()),
            'door-report.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.dashboard.transaction.door-report', [
            'door_reports' => $this->baseQuery()->paginate($this->perPage),
            'locations' => PartitionMaster::where('isdeleted', false)->get()
        ])
        ->layout('components.layouts.dashboard')
        ->title('Door Report');
    }
}