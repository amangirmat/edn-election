<?php

namespace Botble\EdnElection\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\EdnElection\Models\Region;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\RepeaterField;
use Botble\Base\Enums\BaseStatusEnum;
use Illuminate\Support\Facades\DB;

class RegionForm extends FormAbstract
{
    public function buildForm(): void
    {
        $chambers = DB::table('edn_chambers')->pluck('name', 'id')->all();

        $repeaterValue = [];
        if ($this->model->id) {
            $allocations = DB::table('edn_chamber_seats')
                ->where('region_id', $this->model->id)
                ->get();

            foreach ($allocations as $item) {
                $repeaterValue[] = [
                    ['key' => 'chamber_id', 'value' => $item->chamber_id],
                    ['key' => 'seat_count', 'value' => $item->seat_count],
                ];
            }
        }

        $this
            ->setupModel(new Region())
            ->withCustomFields()
            ->add('name', TextField::class, [
                'label' => 'Region Name',
                'required' => true,
                'attr' => ['placeholder' => 'e.g., Oromia']
            ])
            ->add('chamber_allocations', RepeaterField::class, [
                'label' => 'Chamber Seat Allocations',
                'value' => $repeaterValue,
                'fields' => [
                    [
                        'type'       => 'select',
                        'label'      => 'Chamber',
                        'attributes' => [
                            'name'    => 'chamber_id',
                            'list'    => $chambers, // ✅ Botble often uses 'list' for select choices in repeaters
                            'value'   => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'type'       => 'number',
                        'label'      => 'Seats',
                        'attributes' => [
                            'name'    => 'seat_count',
                            'value'   => null,
                            'options' => [
                                'class'       => 'form-control',
                                'placeholder' => '0',
                            ],
                        ],
                    ],
                ],
            ])
            ->setBreakFieldPoint('status')
            ->add('status', 'customSelect', [
                'label'         => trans('core/base::tables.status'),
                'required'      => true,
                'choices'       => BaseStatusEnum::labels(),
                'default_value' => BaseStatusEnum::PUBLISHED,
            ]);
    }
}