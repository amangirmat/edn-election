<?php

namespace Botble\EdnElection\Tables;

use Botble\EdnElection\Models\Election;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\LinkableColumn;
use Botble\Table\Columns\FormattedColumn;
use Illuminate\Database\Eloquent\Builder;

class ElectionTable extends TableAbstract
{
    public function setup(): void
    {
        $this->hasActions = false;
        $this->hasOperations = false;

        $this
            ->model(Election::class)
            ->addColumns([
                IdColumn::make(),
                
                LinkableColumn::make('name')
                    ->title(trans('core/base::tables.name'))
                    ->alignLeft()
                    ->urlUsing(function (LinkableColumn $column) {
                        $item = $column->getItem();
                        return $item ? route('election.summary', ['election_id' => $item->id]) : '#';
                    })
                    ->addClass('font-bold text-primary'),

                // 1. Fixed Type Column
                Column::make('type')
                    ->title('Type')
                    ->alignCenter()
                    ->width(100)
                    ->getValueUsing(function (Column $column) {
                        $item = $column->getItem();
                        if (!$item || !$item->type) return '—';
                        return sprintf(
                            '<span class="badge bg-info text-uppercase" style="font-size: 10px;">%s</span>',
                            clean($item->type)
                        );
                    }),

                // 2. Fixed Status Column (No more dashes)
                FormattedColumn::make('status')
                    ->title(trans('core/base::tables.status'))
                    ->alignCenter()
                    ->width(100)
                    ->getValueUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();
                        if (!$item) return '—';

                        // Manually mapping your status strings to Botble CSS classes
                        $status = $item->status;
                        $class = 'bg-secondary'; // Default color

                        if ($status == 'ongoing') $class = 'bg-success';
                        if ($status == 'draft') $class = 'bg-warning';
                        if ($status == 'completed') $class = 'bg-info';

                        return sprintf(
                            '<span class="badge %s text-uppercase" style="font-size: 10px;">%s</span>',
                            $class,
                            clean($status)
                        );
                    }),

                FormattedColumn::make('results_link')
                    ->title('Results')
                    ->alignCenter()
                    ->width(120)
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();
                        if (! $item) return '';
                        $url = route('election.results.index', ['election_id' => $item->id]);
                        return sprintf(
                            '<a href="%s" class="btn btn-sm btn-success text-white" style="font-size: 11px; padding: 2px 8px;">
                                <i class="fa fa-list-ol"></i> Results
                            </a>', 
                            $url
                        );
                    }),

                FormattedColumn::make('operations')
                    ->title('Operations')
                    ->alignCenter()
                    ->width(100)
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();
                        if (! $item) return '';

                        $editUrl = route('election.edit', $item->id);
                        $deleteUrl = route('election.destroy', $item->id);

                        return sprintf(
                            '<div class="table-actions" style="display: flex; gap: 8px; justify-content: center;">
                                <a href="%s" class="btn btn-icon btn-sm btn-primary" title="Edit"><i class="fa fa-edit"></i></a>
                                <a href="%s" class="btn btn-icon btn-sm btn-danger deleteDialog" title="Delete"><i class="fa fa-trash"></i></a>
                            </div>',
                            $editUrl,
                            $deleteUrl
                        );
                    }),

                CreatedAtColumn::make(),
            ])
            ->addHeaderAction(CreateHeaderAction::make()->route('election.create'));
    }

    public function query(): Builder
    {
        return $this->getModel()->query()->select([
            'id',
            'name',
            'type',   // Must be here
            'status', // Must be here
            'created_at',
        ]);
    }
}