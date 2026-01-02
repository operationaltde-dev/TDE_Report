<?php

namespace App\Livewire\Dashboard\Transaction;

use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Browsershot\Browsershot;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailyMonthlyReport as DailyMonthlyReportExport;
use Carbon\Carbon;

use App\Models\EventLog as EventLogModel;
use App\Models\PartitionMaster;

class DailyMonthlyReport extends Component
{
    use WithPagination;

    public $search = '';
    public $location = '';
    public $typeReport = 'daily';
    public $startDate;
    public $endDate;
    public int $perPage = 10;

    protected $queryString = [
        'search'     => ['except' => ''],
        'location'   => ['except' => ''],
        'typeReport' => ['except' => ''],
        'startDate'  => ['except' => ''],
        'endDate'    => ['except' => ''],
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

    public function updatedTypeReport()
    {
        if ($this->typeReport === 'daily') {
            $this->endDate = $this->startDate;
        }

        if ($this->typeReport === 'monthly' && $this->startDate) {
            $date = Carbon::parse($this->startDate);
            $this->startDate = $date->copy()->startOfMonth()->format('Y-m-d');
            $this->endDate   = $date->copy()->endOfMonth()->format('Y-m-d');
        }

        $this->resetPage();
    }

    public function updatedStartDate()
    {
        if ($this->typeReport === 'daily') {
            $this->endDate = $this->startDate;
        }

        if ($this->typeReport === 'monthly') {
            $date = Carbon::parse($this->startDate);
            $this->startDate = $date->copy()->startOfMonth()->format('Y-m-d');
            $this->endDate   = $date->copy()->endOfMonth()->format('Y-m-d');
        }

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

        $start = $this->startDate
            ? \Carbon\Carbon::parse($this->startDate)->format('Y-m-d')
            : now()->format('Y-m-d');

        $end = $this->endDate
            ? \Carbon\Carbon::parse($this->endDate)->format('Y-m-d')
            : now()->format('Y-m-d');

        $perRow = 20;
        $query = $this->baseQuery();
        $totalRows = $query->count();
        $totalPageGlobal = (int) ceil($totalRows / $perRow);

        $chunkSize = 5000;
        if ($totalRows < $chunkSize) {

            $reports = $query->get();

            $html = view('pdf.daily-monthly-report', [
                'reports'         => $reports,
                'startDate'       => $this->startDate,
                'endDate'         => $this->endDate,
                'typeReport'     => $this->typeReport,
                'logoBase64'      => $logoBase64,
                'pageStart'       => 1,
                'totalPageGlobal' => $totalPageGlobal,
            ])->render();

            $pdfPath = storage_path(
                $this->typeReport=='daily' ? "app/Daily-Report_{$start}.pdf" : "app/Monthly-Report_{$start}_to_{$end}.pdf"
            );

            Browsershot::html($html)
                ->setNodeBinary(env('BROWSERSHOT_NODE_BINARY'))
                ->setNpmBinary(env('BROWSERSHOT_NPM_BINARY'))
                ->noSandbox()
                ->printBackground(true)
                ->format('A4')
                ->landscape()
                ->margins(10, 10, 10, 10)
                ->save($pdfPath);

            return response()->download($pdfPath)->deleteFileAfterSend();
        }

        $globalPage = 1;
        $pdfFiles = [];
        $index = 1;

        $query->chunk($chunkSize, function ($reports) use (
            &$globalPage,
            $perRow,
            $totalPageGlobal,
            $logoBase64,
            &$pdfFiles,
            &$index,
            $start,
            $end
        ) {
            $rowsCount = $reports->count();
            $pageInPdf = (int) ceil($rowsCount / $perRow);

            $html = view('pdf.daily-monthly-report', [
                'reports'         => $reports,
                'startDate'       => $this->startDate,
                'endDate'         => $this->endDate,
                'location'        => $this->location,
                'typeReport'     => $this->typeReport,
                'logoBase64'      => $logoBase64,
                'pageStart'       => $globalPage,
                'totalPageGlobal' => $totalPageGlobal,
            ])->render();

            $path = storage_path(
                $this->typeReport=='daily' 
                    ? "app/temp/Daily-Report_{$start}_Part-{$index}.pdf" 
                    : "app/temp/Monthly-Report_{$start}_to_{$end}_Part-{$index}.pdf"
            );

            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            Browsershot::html($html)
                ->setNodeBinary(env('BROWSERSHOT_NODE_BINARY'))
                ->setNpmBinary(env('BROWSERSHOT_NPM_BINARY'))
                ->noSandbox()
                ->printBackground(true)
                ->format('A4')
                ->landscape()
                ->margins(10, 10, 10, 10)
                ->save($path);

            $pdfFiles[] = $path;
            $globalPage += $pageInPdf;
            $index++;
        });

        $zipPath = storage_path(
            $this->typeReport=='daily' ? "app/Daily-Report_{$start}.zip" : "app/Monthly-Report_{$start}_to_{$end}.zip"
        );

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            foreach ($pdfFiles as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        foreach ($pdfFiles as $file) {
            @unlink($file);
        }

        return response()->download($zipPath)->deleteFileAfterSend();
    }

    public function exportExcel()
    {
        $start = $this->startDate
            ? \Carbon\Carbon::parse($this->startDate)->format('Y-m-d')
            : now()->format('Y-m-d');

        $end = $this->endDate
            ? \Carbon\Carbon::parse($this->endDate)->format('Y-m-d')
            : now()->format('Y-m-d');

        return Excel::download(
            new DailyMonthlyReportExport($this->baseQuery(), $this->typeReport),
            ($this->typeReport=='daily' ? "Daily-Report_{$start}.xlsx" : "Monthly-Report_{$start}_to_{$end}.xlsx")
        );
    }

    public function render()
    {
        return view('livewire.dashboard.transaction.daily-monthly-report', [
            'daily_monthly_reports' => $this->baseQuery()->paginate($this->perPage),
            'typeReport' => $this->typeReport,
            'locations' => PartitionMaster::where('isdeleted', false)->get()
        ])
        ->layout('components.layouts.dashboard')
        ->title('Daily/Monthly Report');
    }
}
