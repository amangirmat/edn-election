<?php

namespace Botble\EdnElection\Imports;

use Botble\EdnElection\Models\Region;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class RegionsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip empty rows
        if (!isset($row['name'])) {
            return null;
        }

        return new Region([
            'name'   => $row['name'],
            'status' => $row['status'] ?? 'published', // Default to published if not provided
        ]);
    }
}