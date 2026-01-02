<?php

namespace App\Livewire\Dashboard\Transaction;

use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Browsershot\Browsershot;
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
        'search'    => ['except' => ''],
        'location'  => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate'   => ['except' => ''],
    ];

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

        $chunkSize = 1000;
        if ($totalRows < $chunkSize) {

            $reports = $query->get();

            $html = view('pdf.list-authorize', [
                'reports'         => $reports,
                'startDate'       => $this->startDate,
                'endDate'         => $this->endDate,
                'location'        => $this->location,
                'logoBase64'      => $logoBase64,
                'pageStart'       => 1,
                'totalPageGlobal' => $totalPageGlobal,
            ])->render();

            $pdfPath = storage_path(
                "app/List-Authorize.pdf"
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

            $html = view('pdf.list-authorize', [
                'reports'         => $reports,
                'startDate'       => $this->startDate,
                'endDate'         => $this->endDate,
                'location'        => $this->location,
                'logoBase64'      => $logoBase64,
                'pageStart'       => $globalPage,
                'totalPageGlobal' => $totalPageGlobal,
            ])->render();

            $path = storage_path(
                "app/temp/List-Authorize_Part-{$index}.pdf"
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
            "app/List-Authorize.zip"
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
        return Excel::download(
            new ListAuthorizeExport($this->baseQuery()),
            'List-Authorize.xlsx'
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