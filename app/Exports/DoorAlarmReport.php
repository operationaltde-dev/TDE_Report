<?php

namespace App\Exports;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class DoorAlarmReport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
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
            'partitionid',
        ]);
    }

    public function map($row): array
    {
        return [
            Carbon::parse($row->ts)->format('d-m-Y H:i:s'),
            $row->msgdescription,
            Str::between($row->hardwaredescription, '[', ']'),
            $row->partition?->description ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Date / Time',
            'Event Name',
            'Door Name',
            'Location',
        ];
    }
}