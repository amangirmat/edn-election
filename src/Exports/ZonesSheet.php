<?php

namespace Botble\EdnElection\Exports;

use Botble\EdnElection\Models\Zone;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ZonesSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        // We load the 'region' relationship to get the name
        return Zone::with('region')->get();
    }

    public function map($zone): array
    {
        return [
            $zone->name,
            $zone->region ? $zone->region->name : 'N/A', // Shows "Oromia" instead of "1"
            $zone->status,
        ];
    }

    public function headings(): array { return ['Name', 'Region', 'Status']; }
    public function title(): string { return 'Zones'; }
}