<?php

namespace Botble\EdnElection\Exports;

use Botble\EdnElection\Models\Region;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RegionsSheet implements FromCollection, WithTitle, WithHeadings
{
    public function collection()
    {
        // Select exactly what you want in the Excel
        return Region::select('name', 'code', 'status')->get();
    }

    public function headings(): array
    {
        return ['Region Name', 'Code', 'Status'];
    }

    public function title(): string
    {
        return 'Regions';
    }
}