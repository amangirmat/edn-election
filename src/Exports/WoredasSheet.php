<?php

namespace Botble\EdnElection\Exports;

use Botble\EdnElection\Models\Woreda;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WoredasSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return Woreda::with('zone')->get();
    }

    public function map($woreda): array
    {
        return [
            $woreda->name,
            $woreda->zone ? $woreda->zone->name : 'N/A',
            $woreda->status,
        ];
    }

    public function headings(): array { return ['Name', 'Zone', 'Status']; }
    public function title(): string { return 'Woredas'; }
}