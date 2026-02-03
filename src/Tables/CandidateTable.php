<?php

namespace Botble\EdnElection\Tables;

use Botble\EdnElection\Models\Candidate;
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

class CandidateTable extends BaseTable
{
    public function setup(): void
    {
        $this
            ->model(Candidate::class)
            ->addColumns([
                IdColumn::make(),
                Column::make('image')
                    ->title('Photo')
                    ->width(70)
                    ->searchable(false)
                    ->orderable(false),
                NameColumn::make()->route('election.candidates.edit'),
                Column::make('party_name')
                    ->title('Party')
                    ->alignLeft()
                    ->orderable(false),
                Column::make('woredas_list')
                    ->title('Woredas')
                    ->alignLeft()
                    ->orderable(false)
                    ->searchable(false),
            ])
            ->addHeaderAction(CreateHeaderAction::make()->route('election.candidates.create'))
            ->addActions([
                EditAction::make()->route('election.candidates.edit'),
                DeleteAction::make()->route('election.candidates.destroy'),
            ]);

        // Centralized helpers from BaseTable
        $this->addImportExportButtons('candidates');
        $this->injectImportAssets('edn.election.import.preview');
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            // Handle Photo rendering
            ->editColumn('image', function (Candidate $item) {
                if (!$item->image) {
                    return null;
                }
                return sprintf(
                    '<img src="%s" width="40" alt="%s" class="img-thumbnail" />',
                    \RvMedia::getImageUrl($item->image, 'thumb'),
                    $item->name
                );
            })
            // Map the woredas names into a simple string
            ->editColumn('woredas_list', function (Candidate $item) {
                return $item->woredas->pluck('name')->implode(', ');
            });

        return $this->toJson($data);
    }

    public function query(): Builder
    {
        return $this->getModel()->query()
            ->select([
                'edn_candidates.id',
                'edn_candidates.name',
                'edn_candidates.image',
                'edn_candidates.party_id',
            ])
            ->join('edn_parties', 'edn_parties.id', '=', 'edn_candidates.party_id')
            ->addSelect('edn_parties.name as party_name')
            ->with(['woredas']); 
    }
}