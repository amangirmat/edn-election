<?php

namespace Botble\EdnElection\Tables;

use Botble\EdnElection\Models\Region;
use Botble\Table\Actions\EditAction;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\Column; // Added for the Code column
use Botble\Table\Columns\StatusColumn;
use Botble\Table\Columns\CreatedAtColumn;

class RegionTable extends BaseTable
{
    public function setup(): void
    {
        $this
            ->model(Region::class)
            ->addColumns([
        IdColumn::make(),
        NameColumn::make()->route('election.regions.edit'),
        Column::make('code')
            ->title('Code')
            ->alignLeft(),
        StatusColumn::make(), // This will now work because of the Model cast
        CreatedAtColumn::make(),
    ])
            ->addHeaderActions([
                CreateHeaderAction::make()->route('election.regions.create'),
            ]);

        // Centralized helpers from BaseTable
        $this->addImportExportButtons('regions');
        $this->injectImportAssets('edn.election.import.preview');

        $this->addActions([
            EditAction::make()->route('election.regions.edit'),
            DeleteAction::make()->route('election.regions.destroy'),
        ]);
    }
}