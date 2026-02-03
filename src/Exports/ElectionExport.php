<?php

namespace Botble\EdnElection\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;
use Botble\EdnElection\Models\Region;
use Botble\EdnElection\Models\Zone;
use Botble\EdnElection\Models\Woreda;
use Botble\EdnElection\Models\Party;
use Botble\EdnElection\Models\Candidate;
use Botble\EdnElection\Models\PollingStation; // Adding Polling just in case

namespace Botble\EdnElection\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class ElectionExport implements FromQuery, WithHeadings, WithMapping
{
    protected string $table;
    protected $model;
    protected ?array $ids; // Add this property

    public function __construct(string $table, ?array $ids = null)
    {
        $this->table = $table;
        $this->ids = (array) $ids; // Ensure it's an array
        
        $this->model = match ($table) {
            'regions'          => new \Botble\EdnElection\Models\Region(),
            'zones'            => new \Botble\EdnElection\Models\Zone(),
            'woredas'          => new \Botble\EdnElection\Models\Woreda(),
            'parties'          => new \Botble\EdnElection\Models\Party(),
            'candidates'       => new \Botble\EdnElection\Models\Candidate(),
            'polling_stations' => new \Botble\EdnElection\Models\PollingStation(),
            'elections'        => new \Botble\EdnElection\Models\Election(),
            default            => new \Botble\EdnElection\Models\Region(),
        };
    }

    public function query(): Builder
    {
        $query = $this->model->query();

        // CRITICAL: If IDs are selected, only export those
        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        return $query;
    }

    public function headings(): array
    {
        return array_map(function($column) {
            return ucwords(str_replace('_', ' ', $column));
        }, $this->model->getFillable());
    }

    public function map($row): array
    {
        $data = [];
        foreach ($this->model->getFillable() as $column) {
            $data[] = $row->{$column};
        }
        return $data;
    }

}