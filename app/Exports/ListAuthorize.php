<?php

namespace App\Exports;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class ListAuthorize implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query->select([
            'ownerid',
            'enddate',
            'partitionid',
        ]);
    }

    public function map($row): array
    {
        return [
            $row->owner?->group?->description,
            $row->owner?->description,
            $row->enddate,
            $row->partition?->description ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Group Name',
            'Full Name',
            'Expired',
            'Location',
        ];
    }
}