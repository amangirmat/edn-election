<?php

namespace Botble\EdnElection\Tables;

use Botble\EdnElection\Models\Region;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\EditAction;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;

class RegionTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Region::class)
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('election.regions.edit'),
                StatusColumn::make(),
            ])
            ->addHeaderAction(CreateHeaderAction::make()->route('election.regions.create'))
            ->addActions([
                EditAction::make()->route('election.regions.edit'),
                DeleteAction::make()->route('election.regions.destroy'),
            ]);
    }
}