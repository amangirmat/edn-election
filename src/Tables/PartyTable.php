<?php

namespace Botble\EdnElection\Tables;

use Botble\EdnElection\Models\Party;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\EditAction;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\Column;
use Illuminate\Database\Eloquent\Builder;

class PartyTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Party::class)
            ->addColumns([
                IdColumn::make(),
                Column::make('logo')
                    ->title('Logo')
                    ->width(70),
                NameColumn::make()->route('election.parties.edit'),
                Column::make('abbreviation')
                    ->title('Abbr.')
                    ->alignLeft(),
                Column::make('color')
                    ->title('Color')
                    ->each(function ($value) {
                        return "<span style='background: $value; padding: 2px 10px; border-radius: 4px;'>$value</span>";
                    }),
            ])
            ->addHeaderAction(CreateHeaderAction::make()->route('election.parties.create'))
            ->addActions([
                EditAction::make()->route('election.parties.edit'),
                DeleteAction::make()->route('election.parties.destroy'),
            ]);
    }

    public function query(): Builder
    {
        return $this->getModel()->query()->select([
            'id', 'name', 'abbreviation', 'logo', 'color'
        ]);
    }
}