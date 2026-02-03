<?php

namespace Botble\EdnElection\Tables;

use Botble\EdnElection\Models\Party;
// Change from TableAbstract to BaseTable
use Botble\EdnElection\Tables\BaseTable;
use Botble\Table\Actions\EditAction;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class PartyTable extends BaseTable
{
    public function setup(): void
    {
        $this
            ->model(Party::class)
            ->addColumns([
                IdColumn::make(),
                Column::make('logo')
                    ->title('Logo')
                    ->width(70)
                    ->searchable(false)
                    ->orderable(false),
                NameColumn::make()->route('election.parties.edit'),
                Column::make('abbreviation')
                    ->title('Abbr.')
                    ->alignLeft(),
                Column::make('color')
                    ->title('Color')
                    ->alignCenter(),
            ])
            ->addHeaderAction(CreateHeaderAction::make()->route('election.parties.create'))
            ->addActions([
                EditAction::make()->route('election.parties.edit'),
                DeleteAction::make()->route('election.parties.destroy'),
            ]);

        // Centralized helpers from BaseTable
        $this->addImportExportButtons('parties');
        $this->injectImportAssets('edn.election.import.preview');
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            // Render the Logo as an Image
            ->editColumn('logo', function (Party $item) {
                if (!$item->logo) {
                    return null;
                }
                return sprintf(
                    '<img src="%s" width="40" alt="%s" class="img-thumbnail" />',
                    \RvMedia::getImageUrl($item->logo, 'thumb'),
                    $item->name
                );
            })
            // Render the Color as a nice badge
            ->editColumn('color', function (Party $item) {
                $color = $item->color ?: '#ccc';
                return sprintf(
                    '<span style="background: %s; padding: 4px 10px; border-radius: 4px; color: #fff; font-family: monospace; text-shadow: 1px 1px 1px rgba(0,0,0,0.3);">%s</span>',
                    $color,
                    $color
                );
            });

        return $this->toJson($data);
    }

    public function query(): Builder
    {
        return $this->getModel()->query()->select([
            'id', 'name', 'abbreviation', 'logo', 'color'
        ]);
    }
}