<?php

namespace App\Exports;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class DailyMonthlyReport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;
    protected string $typeReport;

    public function __construct($query, string $typeReport)
    {
        $this->query = $query;
        $this->typeReport = $typeReport;
    }

    public function query()
    {
        return $this->query->select([
            'ts',
            'credentialid',
            'partitionid',
        ]);
    }

    public function map($row): array
    {
        if ($this->typeReport === 'daily') {
            return [
                Carbon::parse($row->ts)->format('d-m-Y H:i:s'),
                $row->msgdescription,
                $row->credential?->owner?->group?->description,
                Str::between($row->hardwaredescription, '[', ']'),
                $row->credential?->owner?->description,
                $row->partition?->description,
            ];
        }

        if ($this->typeReport === 'monthly') {
            return [
                Carbon::parse($row->ts)->format('d-m-Y H:i:s'),
                $row->credential?->owner?->description ?? '-',
                $row->credential?->description ?? '-',
                $row->credential?->enddate ?? '-',
                $row->partition?->description ?? '-',
            ];
        }
    }

    public function headings(): array
    {
        if ($this->typeReport === 'daily') {
            return [
                'Date / Time',
                'Transaction',
                'Tenant Name',
                'Door Name',
                'Full Name',
                'Location',
            ];
        }

        if ($this->typeReport === 'monthly') {
            return [
                'Date / Time',
                'Full Name',
                'No.Card',
                'Expired',
                'Location',
            ];
        }
    }
}