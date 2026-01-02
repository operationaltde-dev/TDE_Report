<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MatrixAccess implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function map($row): array
    {
        return [
            $row->cardnumber ?? '-',
            $row->full_name ?? '-',
            $row->group_name ?? '-',
            $row->door_name ?? '-',
            $row->location ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Key Card',
            'Full Name',
            'Group Name',
            'Door Name',
            'Location',
        ];
    }
}