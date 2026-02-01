<?php

namespace Botble\EdnElection\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\EdnElection\Models\Election;
use Botble\EdnElection\Http\Requests\ElectionRequest;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\DateField;

class ElectionForm extends FormAbstract
{
    public function buildForm(): void
    {
        $this
            ->setupModel(new Election())
            ->setValidatorClass(ElectionRequest::class)
            ->withCustomFields()
            ->add('name', TextField::class, [
                'label'      => 'Election Name',
                'attr'       => ['placeholder' => 'e.g., 2026 General Election'],
                'required'   => true,
            ])
            ->add('type', SelectField::class, [
                'label'      => 'Election Type',
                'choices'    => [
                    'national' => 'National',
                    'regional' => 'Regional',
                    'referendum' => 'Referendum',
                ],
            ])
            ->add('election_date', DateField::class, [
                'label'      => 'Election Date',
                'default_value' => now()->format('Y-m-d'),
            ])
            ->add('status', SelectField::class, [
                'label'      => 'Status',
                'choices'    => [
                    'upcoming' => 'Upcoming',
                    'ongoing'  => 'Ongoing',
                    'counting' => 'Counting',
                    'final'    => 'Final Results',
                ],
            ]);
    }
}