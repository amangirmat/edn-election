<?php

namespace Botble\EdnElection\Tables;

use Botble\EdnElection\Models\Chamber;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse; // <--- MUST ADD THIS
use Botble\Table\Actions\EditAction;
use Botble\Table\Actions\DeleteAction;
use Botble\Base\Facades\Html;

class ChamberTable extends TableAbstract
{
    public function query(): Builder
    {
        $query = Chamber::query()->select([
            'id',
            'name',
            'level',
            'total_seats',
            'status', // Added status to the query
            'created_at',
        ]);

        return $this->applyScopes($query);
    }

    public function ajax(): JsonResponse
{
    $data = $this->table
        ->eloquent($this->query())
        ->editColumn('name', function (Chamber $item) {
            // This makes the name clickable to go to the edit page
            return Html::link(route('election.chambers.edit', $item->id), $item->name);
        })
        ->editColumn('status', function (Chamber $item) {
            // Safe check: if it's an Enum use toHtml(), otherwise just return the string
            return is_object($item->status) && method_exists($item->status, 'toHtml') 
                ? $item->status->toHtml() 
                : $item->status;
        })
        ->addColumn('operations', function (Chamber $item) {
            return $this->getOperations('election.chambers.edit', 'election.chambers.destroy', $item);
        });

    return $this->toJson($data);
}

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'level' => [
                'title' => 'Level',
                'class' => 'text-start',
            ],
            'total_seats' => [
                'title' => 'Total Seats',
                'class' => 'text-center',
            ],
            'status' => [ // Added status column
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'operations' => [
                'title' => trans('core/base::tables.operations'),
                'width' => '134px',
                'class' => 'text-center',
                'orderable' => false,
                'searchable' => false,
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('election.chambers.create'), 'election.chambers.create');
    }
}