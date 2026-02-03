<?php

namespace EdnElection\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ElectionMasterImport implements WithMultipleSheets 
{
    public function sheets(): array
    {
        return [
            'Regions' => new RegionsSheetImport(),
            'Zones'   => new ZonesSheetImport(),
            'Woredas' => new WoredasSheetImport(),
            'Parties' => new PartiesSheetImport(),
            'Results' => new ResultsSheetImport(),
        ];
    }
}