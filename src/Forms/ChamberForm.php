<?php

namespace Botble\EdnElection\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\RepeaterField;
use Botble\EdnElection\Http\Requests\ChamberRequest;
use Botble\EdnElection\Models\Chamber;
use Illuminate\Support\Facades\DB;

class ChamberForm extends FormAbstract
{
    public function buildForm(): void
    {
        // 1. Fetch regions for the dropdown
        $regions = DB::table('edn_regions')->pluck('name', 'id')->all();

        // 2. Format existing data for the repeater (essential for the 'Edit' page)
        $repeaterValue = [];
        if ($this->model && $this->model->regional_seats) {
            foreach ($this->model->regional_seats as $item) {
                $repeaterValue[] = [
                    ['key' => 'region_id', 'value' => $item['region_id'] ?? ''],
                    ['key' => 'seat_count', 'value' => $item['seat_count'] ?? 0],
                ];
            }
        }

        // 3. Ensure we have a model instance to avoid the "::class" error
        $model = $this->model ?: new Chamber();

        $this
            ->setupModel($model)
            ->setValidatorClass(ChamberRequest::class)
            ->withCustomFields() // Required to enable the RepeaterField type
            ->add('name', TextField::class, [
                'label' => 'Chamber Name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter chamber name',
                ],
            ])
            ->add('level', SelectField::class, [
                'label' => 'Level',
                'required' => true,
                'choices' => [
                    'federal' => 'Federal',
                    'regional' => 'Regional',
                ],
            ])
            
            ->add('election_id', 'customSelect', [
    'label'      => 'Linked Election',
    'choices'    => ['' => '-- Select Election --'] + \Botble\EdnElection\Models\Election::pluck('name', 'id')->all(),
    'attr'       => ['class' => 'form-control select-full'],
])


            ->add('total_seats', NumberField::class, [
                'label' => 'Total Seats',
                'required' => true,
                'attr' => [
                    'placeholder' => '0',
                ],
            ])
            ->add('regional_seats', RepeaterField::class, [
                'label' => 'Regional Seat Allocation',
                'value' => $repeaterValue,
                'fields' => [
                    [
                        'type'    => 'select',
                        'label'   => 'Region',
                        'attributes' => [
                            'name'    => 'region_id',
                            'choices' => $regions,
                        ],
                    ],
                    [
                        'type'    => 'number',
                        'label'   => 'Seats',
                        'attributes' => [
                            'name'    => 'seat_count',
                        ],
                    ],
                ],
            ]);
    }
}