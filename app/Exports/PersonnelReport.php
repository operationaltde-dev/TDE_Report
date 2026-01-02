<?php

namespace App\Exports;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class PersonnelReport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query->select([
            'ts',
            'msgdescription',
            'hardwaredescription',
            'credentialid',
            'partitionid',
        ]);
    }

    public function map($row): array
    {
        return [
            $row->credential?->cardnumber ?? '-',
            $row->credential?->owner?->description ?? '-',
            $row->credential?->owner?->group?->description ?? '-',
            Str::between($row->hardwaredescription, '[', ']'),
            \Carbon\Carbon::parse($row->ts)->format('Y-m-d'),
            \Carbon\Carbon::parse($row->ts)->format('H:i:s'),
            \Carbon\Carbon::parse($row->ts_received)->format('Y-m-d'),
            \Carbon\Carbon::parse($row->ts_received)->format('H:i:s'),
            $row->partition?->description ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'No.Card',
            'Full Name',
            'Group Name',
            'Door Name',
            'Start Date',
            'Start Time',
            'End Date',
            'End Time',
            'Location',
        ];
    }
}