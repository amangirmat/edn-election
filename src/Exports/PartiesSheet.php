<?php

namespace Botble\EdnElection\Exports;

use Botble\EdnElection\Models\Party;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PartiesSheet implements FromCollection, WithTitle, WithHeadings
{
    public function collection() { return Party::select('name', 'abbreviation', 'logo', 'color')->get(); }
    public function headings(): array { return ['Party Name', 'Abbreviation', 'Logo', 'Color']; }
    public function title(): string { return 'Parties'; }
}