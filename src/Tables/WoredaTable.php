<?php

namespace Botble\EdnElection\Tables;

use Botble\EdnElection\Models\Woreda;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\EditAction;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\HeaderActions\HeaderAction; // Fix: Ensure this matches the package path
use Botble\Table\HeaderActions\CreateHeaderAction;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\Column;
use Illuminate\Database\Eloquent\Builder;

class WoredaTable extends BaseTable
{
    public function setup(): void
    {
        $this
            ->model(Woreda::class)
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('election.woredas.edit'),
                Column::make('zone_name')->title('Zone')->alignLeft(),
                Column::make('total_voters')->title('Voters')->width(100),
            ])
            ->addHeaderActions([
                CreateHeaderAction::make()->route('election.woredas.create'),
            ]);

        // 1. Add the Import/Export buttons using the BaseTable helper
        $this->addImportExportButtons('woredas');

        // 2. Inject the JS assets (points to the preview route)
        $this->injectImportAssets('edn.election.import.preview');
    }

    public function query(): Builder
    {
        $query = $this->getModel()->query()
            ->select([
                'edn_woredas.id',
                'edn_woredas.name',
                'edn_woredas.zone_id',
                'edn_woredas.total_voters',
            ])
            ->join('edn_zones', 'edn_zones.id', '=', 'edn_woredas.zone_id')
            ->addSelect('edn_zones.name as zone_name');

        return $query;
    }
}