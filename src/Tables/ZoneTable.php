<?php

namespace Botble\EdnElection\Tables;

use Botble\EdnElection\Models\Zone;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\EditAction;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\Column;
use Illuminate\Database\Eloquent\Builder;

class ZoneTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Zone::class)
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('election.zones.edit'),
                Column::make('region_name')->title('Region')->alignLeft(),
            ])
            ->addHeaderAction(CreateHeaderAction::make()->route('election.zones.create'))
            ->addActions([
                EditAction::make()->route('election.zones.edit'),
                DeleteAction::make()->route('election.zones.destroy'),
            ]);
    }

    public function query(): Builder
    {
        return $this->getModel()->query()
            ->select([
                'edn_zones.id',
                'edn_zones.name',
                'edn_zones.region_id',
            ])
            ->join('edn_regions', 'edn_regions.id', '=', 'edn_zones.region_id')
            ->addSelect('edn_regions.name as region_name');
    }
}