<?php

namespace Botble\EdnElection\Tables;

use Botble\EdnElection\Models\Candidate;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\EditAction;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\Column;
use Illuminate\Database\Eloquent\Builder;

class CandidateTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Candidate::class)
            ->addColumns([
                IdColumn::make(),
                Column::make('image')->title('Photo')->width(70),
                NameColumn::make()->route('election.candidates.edit'),
                Column::make('party_name')
                    ->title('Party')
                    ->alignLeft()
                    ->orderable(false),
                // Safe Woredas column
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
    }

    public function ajax(): \Illuminate\Http\JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            // Use editColumn to map the woredas names into a simple string
            ->editColumn('woredas_list', function (Candidate $item) {
                return $item->woredas->pluck('name')->implode(', ');
            });

        return $this->toJson($data);
    }

    public function query(): Builder
    {
        // Using your exact working query with with('woredas') added
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